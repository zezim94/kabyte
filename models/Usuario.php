<?php
require_once __DIR__ . '/../config/Database.php';

class Usuario
{
    public static function buscarPorLogin($login)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarPorEmail($email)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function salvarGoogle($dados)
    {
        $pdo = Database::connect();

        // 1. Tenta achar alguém que já tenha esse e-mail (mesmo que tenha se cadastrado com senha antes)
        $existente = self::buscarPorEmail($dados['email']);

        if ($existente) {
            // USUÁRIO JÁ EXISTE: Só atualiza a foto e o ID do Google para vincular
            $sql = "UPDATE usuarios SET google_id = ?, foto = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$dados['id'], $dados['picture'], $existente['id']]);

            // Retorna os dados atualizados para logar
            return self::buscarPorEmail($dados['email']);
        } else {
            // USUÁRIO NOVO: Cria um usuário e senha vazia
            // Gera um "login" automático pegando a parte antes do @ do email
            $loginAutomatico = explode('@', $dados['email'])[0];

            // Verifica se esse login automático já existe, se sim, adiciona numeros aleatórios
            $testeLogin = self::buscarPorLogin($loginAutomatico);
            if ($testeLogin) {
                $loginAutomatico .= rand(100, 999);
            }

            $sql = "INSERT INTO usuarios (nome, email, google_id, foto, nivel, usuario, senha) VALUES (?, ?, ?, ?, 'cliente', ?, '')";

            $pdo->prepare($sql)->execute([
                $dados['name'],
                $dados['email'],
                $dados['id'],
                $dados['picture'],
                $loginAutomatico // Aqui salvamos um login gerado para ele
            ]);

            return self::buscarPorEmail($dados['email']);
        }
    }

    // Método para o Admin ver faturamento
    public static function getFaturamentoHoje()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT SUM(total) as total, COUNT(*) as qtd FROM vendas WHERE DATE(data_venda) = CURDATE()");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getFaturamentoPorPagamento()
    {
        $pdo = Database::connect();
        $sql = "SELECT forma_pagamento, SUM(total) as total, COUNT(*) as qtd 
                FROM vendas 
                WHERE DATE(data_venda) = CURDATE() 
                GROUP BY forma_pagamento";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para o Master ver logs
    public static function getLogs()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT l.*, u.nome FROM sistema_logs l LEFT JOIN usuarios u ON l.usuario_id = u.id ORDER BY l.id DESC LIMIT 50");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Em models/Usuario.php
    public static function getUltimasVendas()
    {
        $pdo = Database::connect();

        // CORREÇÃO: Adicionei v.forma_pagamento e v.status_pagamento na lista
        $sql = "SELECT 
                    v.id, 
                    v.data_venda, 
                    v.total, 
                    v.forma_pagamento, 
                    v.status_pagamento, 
                    u.nome as vendedor 
                FROM vendas v 
                LEFT JOIN usuarios u ON v.usuario_id = u.id 
                ORDER BY v.id DESC LIMIT 10";

        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarParaAdmin()
    {
        $pdo = Database::connect();
        // Não selecionamos google_id nem foto, como você pediu
        $sql = "SELECT id, nome, email, usuario, nivel FROM usuarios WHERE nivel != 'master' ORDER BY nome ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT id, nome, email, usuario, nivel FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function atualizar($id, $dados)
    {
        $pdo = Database::connect();

        if (isset($dados['senha'])) {
            // Atualiza com senha
            $sql = "UPDATE usuarios SET nome=?, email=?, usuario=?, nivel=?, senha=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$dados['nome'], $dados['email'], $dados['usuario'], $dados['nivel'], $dados['senha'], $id]);
        } else {
            // Atualiza sem mexer na senha
            $sql = "UPDATE usuarios SET nome=?, email=?, usuario=?, nivel=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$dados['nome'], $dados['email'], $dados['usuario'], $dados['nivel'], $id]);
        }
    }

    public static function excluir($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Insere um novo usuário administrador/vendedor no banco
    public static function salvarNovo($dados)
    {
        $pdo = Database::connect();

        $sql = "INSERT INTO usuarios (nome, email, usuario, nivel, senha) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            $dados['nome'],
            $dados['email'],
            $dados['usuario'],
            $dados['nivel'],
            $dados['senha']
        ]);
    }
}