<?php
require_once __DIR__ . '/../models/Chave.php';

class MercadoPagoService
{
    public function processarPagamentoBrick($dados)
    {
        $url = "https://api.mercadopago.com/v1/payments";

        $dados['transaction_amount'] = round((float) $dados['transaction_amount'], 2);
        
        if (isset($dados['issuer_id']) && !empty($dados['issuer_id'])) {
            $dados['issuer_id'] = (int) $dados['issuer_id'];
        }

        // BUSCANDO DO BANCO DE DADOS
        $token = Chave::get('mp_access_token');
        
        if (empty($token)) {
            return ['status' => 'error', 'status_detail' => 'Token do Mercado Pago não encontrado no banco de dados.'];
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($dados),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token, // INJETA A CHAVE DO BANCO AQUI
                "X-Idempotency-Key: " . uniqid()
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}