<?php
// Arquivo: services/MercadoPagoService.php

class MercadoPagoService
{
    public function processarPagamentoBrick($dados)
    {
        $url = "https://api.mercadopago.com/v1/payments";

        // 1. Tratamento de valores para evitar erro de tipo
        $dados['transaction_amount'] = round((float) $dados['transaction_amount'], 2);
        
        // Garante que o issuer_id seja inteiro se existir (Obrigatório para alguns cartões)
        if (isset($dados['issuer_id']) && !empty($dados['issuer_id'])) {
            $dados['issuer_id'] = (int) $dados['issuer_id'];
        }

        // 2. DEBUG DE CHAVES (Isso vai salvar no log qual chave está sendo usada)
        // Se a constante não existir, avisa no log
        $token = defined('MP_ACCESS_TOKEN') ? MP_ACCESS_TOKEN : 'ERRO_SEM_TOKEN_NO_CONFIG';
        
        $logFile = __DIR__ . '/../debug_mp.log';
        file_put_contents($logFile, "--- DISPARO CURL ---\n", FILE_APPEND);
        file_put_contents($logFile, "Token usado (inicio): " . substr($token, 0, 10) . "...\n", FILE_APPEND);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($dados),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token, // AQUI: Usa a constante do config.php
                "X-Idempotency-Key: " . uniqid()
            ],
            CURLOPT_SSL_VERIFYPEER => false // Em produção mantenha true
        ]);

        $response = curl_exec($ch);
        
        // Log de erro de conexão (DNS, Internet, SSL)
        if (curl_errno($ch)) {
            file_put_contents($logFile, "ERRO CURL: " . curl_error($ch) . "\n", FILE_APPEND);
        }
        
        curl_close($ch);

        return json_decode($response, true);
    }
}