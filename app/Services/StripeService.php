<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeService {
    public function __construct() {
        // Inicializa o Stripe com a chave secreta
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    }

    public function createCheckoutSession(int $userId, int $planId, string $userEmail) {
        return Session::create([
            'payment_method_types' => ['card'], // Adicione 'alipay' ou outros se necessário
            'line_items' => [[
                'price' => ($planId == 2 ? $_ENV['STRIPE_PRICE_PREMIUM'] : $_ENV['STRIPE_PRICE_BASIC']),
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => BASE_URL . '/plans/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => BASE_URL . '/plans',
            'customer_email' => $userEmail,
            'metadata' => [
                'user_id' => $userId,
                'plan_id' => $planId
            ]
        ]);
    }

    /**
     * Processa o Webhook do Stripe para confirmar eventos.
     */
    public function handleWebhook() {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? ''; 
        $event = null;

        // A chave secreta do Webhook deve ser carregada do .env
        $webhookSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? 'sua_chave_secreta_aqui';

        try {
            $event = Webhook::constructEvent(
                $payload, 
                $sigHeader, 
                $webhookSecret
            );
        } catch(SignatureVerificationException $e) {
            // Assinatura inválida (ERRO 400)
            error_log("Stripe Webhook Error: Invalid signature: " . $e->getMessage());
            http_response_code(400); 
            exit;
        } catch (\UnexpectedValueException $e) {
             // Payload inválido (ERRO 400)
            error_log("Stripe Webhook Error: Invalid payload: " . $e->getMessage());
            http_response_code(400); 
            exit;
        }

        return $event;
    }
}