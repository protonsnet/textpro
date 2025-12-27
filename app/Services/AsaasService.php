<?php
namespace App\Services;

class AsaasService {
    private string $apiKey;
    private string $apiUrl;

    public function __construct() {
        $this->apiKey = $_ENV['ASAAS_API_KEY'];
        $this->apiUrl = $_ENV['ASAAS_API_URL']; // https://api.asaas.com/v3 ou sandbox
    }

    private function request($endpoint, $method = 'GET', $data = []) {
        $url = $this->apiUrl . $endpoint; // Corrigido de baseUrl para apiUrl
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $headers = [
            'Content-Type: application/json',
            'access_token: ' . $this->apiKey,
            'User-Agent: TextPro/1.0'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res = json_decode($response);
        
        // Se houver erro de rede ou JSON inválido
        if (!$res) {
            return (object)['errors' => [(object)['description' => 'Falha na comunicação com o gateway.']]];
        }

        return $res;
    }

    public function createCustomer($user) {
        $user = (object)$user;

        // Limpeza de campos para evitar erros de 'null' no PHP 8
        $document = preg_replace('/\D/', '', $user->cpf_cnpj ?? '');
        $nome = trim($user->nome ?? '');
        $email = trim($user->email ?? '');
        $telefone = preg_replace('/\D/', '', $user->telefone ?? '');

        $data = [
            'name' => $nome,
            'email' => $email,
            'cpfCnpj' => $document,
            'mobilePhone' => $telefone,
            'externalReference' => $user->id ?? null,
            'notificationDisabled' => true 
        ];

        return $this->request('/customers', 'POST', $data);
    }

    public function createBilling($customerId, $amount, $dueDate, $description) {
        $data = [
            'customer' => $customerId,
            'billingType' => 'UNDEFINED',
            'value' => $amount,
            'dueDate' => $dueDate,
            'description' => $description,
            'postalService' => false
        ];

        return $this->request('/payments', 'POST', $data);
    }

    public function confirmPaymentInAsaas($asaasId, $value, $date) {
        $url = $this->baseUrl . "/payments/" . $asaasId . "/receiveInCash";
        
        $data = [
            "paymentDate" => $date,
            "value" => $value
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "access_token: " . $this->apiKey
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    /**
     * Verifica o status de um pagamento e, se estiver pendente, baixa manualmente no cash
     */
    public function processManualCashing($asaasBillingId, $amount) {
        // 1. Consultar status atual no Asaas
        $payment = $this->request("/payments/{$asaasBillingId}", 'GET');
        
        if (isset($payment->errors)) return $payment;

        // 2. Se já estiver recebido no Asaas, apenas retornamos sucesso para atualizar o local
        if ($payment->status === 'RECEIVED' || $payment->status === 'CONFIRMED') {
            return $payment;
        }

        // 3. Se estiver pendente ou vencido, forçar o recebimento em dinheiro (cash)
        $data = [
            "paymentDate" => date('Y-m-d'),
            "value" => $amount
        ];

        return $this->request("/payments/{$asaasBillingId}/receiveInCash", 'POST', $data);
    }
}