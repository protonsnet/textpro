<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\StripeService;
use App\Services\AsaasService; 
use App\Models\SystemUserModel;
use App\Models\SubscriptionModel;
use App\Models\PlanModel;
use App\Core\Database;
class WebhookController extends Controller
{
    protected StripeService $stripeService;
    protected SystemUserModel $userModel;
    protected SubscriptionModel $subscriptionModel;
    protected PlanModel $planModel;

    public function __construct()
    {
        parent::__construct();
        $this->stripeService     = new StripeService();
        $this->userModel         = new SystemUserModel();
        $this->subscriptionModel = new SubscriptionModel();
        $this->planModel         = new PlanModel();
    }

    /**
     * =========================================================
     * WEBHOOK ASAAS
     * =========================================================
     */
    public function handleAsaas()
    {
        // 1. Segurança: Validação do Token (Idêntico ao sistema que funciona)
        $tokenDefinido = '96A39F773176C3C891D145401BA0146522FE7713';
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $tokenRecebido = $headers['asaas-access-token'] ?? ($_SERVER['HTTP_ASAAS_ACCESS_TOKEN'] ?? '');

        if ($tokenRecebido !== $tokenDefinido) {
            http_response_code(401);
            exit("Token inválido");
        }

        $body = file_get_contents('php://input');
        $data = json_decode($body);

        if (!$data || !isset($data->event)) {
            http_response_code(400);
            exit("Dados inválidos");
        }

        $db = Database::getInstance();
        $eventsPagamento = ['PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED', 'PAYMENT_RECEIVED_IN_CASH'];

        if (in_array($data->event, $eventsPagamento)) {
            $asaasId = $data->payment->id; // pay_nlpq1fq7ntxishoc
            $clienteAsaasId = $data->payment->customer; // cus_...

            try {
                $asaasId = $data->payment->id;
                $clienteAsaasId = $data->payment->customer;
                $valorPago = (float) $data->payment->value;

                // 1. Marcar pagamento como confirmado
                $db->prepare("UPDATE payments SET status = 'CONFIRMED', data_pagamento = NOW() WHERE asaas_billing_id = ?")
                ->execute([$asaasId]);

                // 2. Localizar Usuário
                $stmtUser = $db->prepare("SELECT id FROM system_users WHERE asaas_customer_id = ?");
                $stmtUser->execute([$clienteAsaasId]);
                $userLocal = $stmtUser->fetch(\PDO::FETCH_OBJ);

                if ($userLocal) {
                    $userId = $userLocal->id;

                    // 3. BUSCA INTELIGENTE DE PLANO (Valor mais próximo)
                    // Busca o plano onde a diferença entre o preço e o valor pago seja a menor possível
                    $stmtPlan = $db->prepare("SELECT id FROM plans ORDER BY ABS(preco - ?) ASC LIMIT 1");
                    $stmtPlan->execute([$valorPago]);
                    $plan = $stmtPlan->fetch(\PDO::FETCH_OBJ);
                    $planId = $plan ? $plan->id : 1; // Fallback para plano 1

                    // 4. Liberar Usuário
                    $db->prepare("UPDATE system_users SET suspenso = 0, ativo = 1 WHERE id = ?")->execute([$userId]);

                    // 5. Gestão de Assinatura (Cria ou Atualiza)
                    $stmtSub = $db->prepare("SELECT id FROM subscriptions WHERE user_id = ? LIMIT 1");
                    $stmtSub->execute([$userId]);
                    $subId = $stmtSub->fetchColumn();

                    $vencimento = date('Y-m-d', strtotime('+30 days'));

                    if ($subId) {
                        $db->prepare("UPDATE subscriptions SET status = 'active', plan_id = ?, proximo_vencimento = ? WHERE id = ?")
                        ->execute([$planId, $vencimento, $subId]);
                    } else {
                        $db->prepare("INSERT INTO subscriptions (user_id, plan_id, status, proximo_vencimento, data_inicio) VALUES (?, ?, 'active', ?, NOW())")
                        ->execute([$userId, $planId, $vencimento]);
                        $subId = $db->lastInsertId();
                    }

                    // 6. Vincula pagamento à assinatura
                    $db->prepare("UPDATE payments SET subscription_id = ? WHERE asaas_billing_id = ?")
                    ->execute([$subId, $asaasId]);
                }
            } catch (\Exception $e) {
                error_log("Erro Webhook: " . $e->getMessage());
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 'success']);
        exit;
    }

    /**
     * =========================================================
     * WEBHOOK STRIPE (MÉTODO QUE VOCÊ JÁ TINHA)
     * =========================================================
     */
    public function handleStripe()
    {
        $event = $this->stripeService->handleWebhook();

        if (!$event) {
            http_response_code(400);
            exit;
        }

        // Log opcional
        // error_log('Stripe event: ' . $event->type);

        switch ($event->type) {

            /**
             * =========================================================
             * A. CHECKOUT FINALIZADO (CRIAÇÃO DA ASSINATURA)
             * =========================================================
             */
            case 'checkout.session.completed':
                $session = $event->data->object;

                if (
                    $session->mode !== 'subscription' ||
                    empty($session->subscription) ||
                    $session->payment_status !== 'paid'
                ) {
                    break;
                }

                $this->handleSubscriptionCreation($session);
                break;

            /**
             * =========================================================
             * B. PAGAMENTO RECORRENTE BEM SUCEDIDO
             * =========================================================
             */
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                if (!empty($invoice->subscription)) {
                    // 1. Atualiza a assinatura
                    $this->subscriptionModel->updateStatusByStripeId($invoice->subscription, 'active');
                    
                    // 2. Reativa o usuário no banco (NOVO)
                    $sub = $this->subscriptionModel->findByStripeId($invoice->subscription);
                    if ($sub) {
                        $this->userModel->reactivate($sub->user_id);
                    }
                }
                break;

            /**
             * =========================================================
             * C. FALHA DE PAGAMENTO
             * =========================================================
             */
            case 'invoice.payment_failed':
                $invoice = $event->data->object;

                if (!empty($invoice->subscription)) {
                    $this->subscriptionModel
                        ->updateStatusByStripeId($invoice->subscription, 'past_due');
                }
                break;

            /**
             * =========================================================
             * D. ATUALIZAÇÃO DA ASSINATURA (PLANO / STATUS)
             * =========================================================
             */
            case 'customer.subscription.updated':
                $subscription = $event->data->object;

                // Atualiza status
                $this->subscriptionModel
                    ->updateStatusByStripeId($subscription->id, $subscription->status);

                // Atualiza plano, se mudou
                if (!empty($subscription->items->data[0]->price->id)) {
                    $newPriceId = $subscription->items->data[0]->price->id;
                    $this->subscriptionModel
                        ->updatePlanByStripeId($subscription->id, $newPriceId);
                }
                break;

            /**
             * =========================================================
             * E. CANCELAMENTO DEFINITIVO
             * =========================================================
             */
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $this->subscriptionModel->cancelSubscription($subscription->id);
                break;

            default:
                // Evento ignorado
        }

        http_response_code(200);
        exit;
    }

    /**
     * =========================================================
     * CRIAÇÃO DA ASSINATURA NO BANCO (IDEMPOTENTE)
     * =========================================================
     */
    private function handleSubscriptionCreation($session)
    {
        $customerEmail         = $session->customer_details->email ?? null;
        $stripeSubscriptionId  = $session->subscription;

        if (!$customerEmail || !$stripeSubscriptionId) {
            return;
        }

        /**
         * IMPORTANTE:
         * checkout.session NÃO contém line_items por padrão no webhook.
         * O Price ID deve ser recuperado via Subscription.
         */
        $stripeSubscription = $this->stripeService
            ->retrieveSubscription($stripeSubscriptionId);

        if (empty($stripeSubscription->items->data[0]->price->id)) {
            return;
        }

        $stripePriceId = $stripeSubscription->items->data[0]->price->id;

        $user = $this->userModel->findByEmail($customerEmail);
        $plan = $this->planModel->findByStripePriceId($stripePriceId);

        if (!$user || !$plan) {
            return;
        }

        /**
         * IDEMPOTÊNCIA:
         * Evita criar a mesma assinatura duas vezes
         */
        if ($this->subscriptionModel->existsByStripeId($stripeSubscriptionId)) {
            return;
        }

        $this->subscriptionModel->createSubscription(
            $user->id,
            $plan->id,
            $stripeSubscriptionId,
            $stripeSubscription->status // active | trialing
        );
    }
}