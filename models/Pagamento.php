<?php
require_once __DIR__ . '/../config/Database.php';

class Pagamento
{
    /**
     * Salva o registro do pagamento atrelado a uma venda
     * Passamos o $pdo por parâmetro para manter dentro da mesma transação da Venda
     */
    public static function salvar($pdo, $dados)
    {
        $sql = "INSERT INTO pagamentos (
                    venda_id, cliente_id, mp_id, status, descricao, 
                    transaction_details, valor, data_criacao, 
                    payment_type, payment_method, installments, 
                    installment_value, total_paid
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $dados['venda_id'],
            $dados['cliente_id'],
            $dados['mp_id'],
            $dados['status'],
            $dados['descricao'],
            $dados['transaction_details'],
            $dados['valor'],
            $dados['payment_type'],
            $dados['payment_method'],
            $dados['installments'],
            $dados['installment_value'],
            $dados['total_paid']
        ]);
        
        return $pdo->lastInsertId();
    }

    /**
     * Busca o último pagamento associado a uma venda específica
     */
    public static function buscarPorVenda($vendaId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM pagamentos WHERE venda_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$vendaId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}