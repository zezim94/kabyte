<?php
require_once __DIR__ . '/../config/Database.php';

class Endereco
{
    // Listar endereços de um cliente específico
    public static function listarPorCliente($clienteId)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM enderecos WHERE cliente_id = ?");
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Salvar novo endereço
    public static function salvar($dados)
    {
        $pdo = Database::connect();
        $sql = "INSERT INTO enderecos (cliente_id, logradouro, numero, bairro, cidade, cep, complemento) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $dados['cliente_id'],
            $dados['logradouro'],
            $dados['numero'],
            $dados['bairro'],
            $dados['cidade'],
            $dados['cep'],
            $dados['complemento'] ?? ''
        ]);
    }
}