<?php
require_once __DIR__ . '/../config/Database.php';

class Cliente
{
    // Lista os clientes (Apenas dados pessoais básicos)
    public static function listar()
    {
        $pdo = Database::connect();
        return $pdo->query("SELECT id, nome, cpf, telefone, email, data_cadastro FROM clientes ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca o cliente pelo ID. 
    // NOTA: Como o endereço agora fica em outra tabela, não precisamos puxar no JOIN aqui para não duplicar linhas caso ele tenha 2 endereços.
    public static function buscarPorId($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Salva o cliente pelo Painel Admin (Agora só salva dados pessoais)
    public static function salvar($d)
    {
        $pdo = Database::connect();
        $sql = "INSERT INTO clientes (nome, cpf, telefone, email) VALUES (?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $d['nome'],
            $d['cpf'],
            $d['telefone'],
            $d['email']
        ]);
    }

    // Atualiza os dados do cliente pelo Painel Admin
    public static function atualizar($id, $dados)
    {
        $pdo = Database::connect();

        try {
            // 1. Lógica da Senha (só altera se o usuário digitou algo novo)
            $sqlSenha = "";
            if (!empty($dados['senha'])) {
                $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);
                $sqlSenha = ", senha = '$senhaHash'";
            }

            // 2. Monta a Query (Removemos as colunas de endereço daqui)
            $sql = "UPDATE clientes SET 
                    nome = ?, cpf = ?, telefone = ?, email = ?
                    $sqlSenha
                    WHERE id = ?";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $dados['nome'],
                $dados['cpf'],
                $dados['telefone'],
                $dados['email'],
                $id
            ]);

            return ['sucesso' => true, 'msg' => 'Dados atualizados com sucesso!'];

        } catch (Exception $e) {
            return ['sucesso' => false, 'msg' => 'Erro ao atualizar: ' . $e->getMessage()];
        }
    }

    // Exclui o cliente.
    // GRAÇAS ao "ON DELETE CASCADE" que está no banco, os endereços dele serão excluídos sozinhos!
    public static function excluir($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Busca para Login manual ou verificação de duplicidade
    public static function buscarPorEmail($email)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método principal para o Login com Google
    public static function salvarGoogle($dados)
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE email = ?");
        $stmt->execute([$dados['email']]);
        $existente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existente) {
            // Atualiza os dados do Google se o e-mail já existir
            $sql = "UPDATE clientes SET google_id = ?, foto = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$dados['id'], $dados['picture'], $existente['id']]);
            return self::buscarPorEmail($dados['email']);
        } else {
            // Insere novo cliente vindo do Google
            $sql = "INSERT INTO clientes (nome, email, google_id, foto, data_cadastro) VALUES (?, ?, ?, ?, NOW())";
            $pdo->prepare($sql)->execute([
                $dados['name'],
                $dados['email'],
                $dados['id'],
                $dados['picture']
            ]);
            return self::buscarPorEmail($dados['email']);
        }
    }

    // Usado na tela de Esqueci minha senha
    public static function atualizarSenha($id, $novaSenhaHash)
    {
        $pdo = Database::connect();
        $sql = "UPDATE clientes SET senha = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$novaSenhaHash, $id]);
    }

    // Registra um cliente novo pelo formulário do site (Vitrine)
    public static function registrarNovoCliente($dados)
    {
        $pdo = Database::connect();

        $sql = "INSERT INTO clientes (nome, email, telefone, senha, data_cadastro) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);

        $sucesso = $stmt->execute([
            $dados['nome'],
            $dados['email'],
            $dados['telefone'],
            $dados['senha']
        ]);

        if ($sucesso) {
            return $pdo->lastInsertId(); // Devolve o ID real
        }

        return false;
    }

    /* =========================================================================
       NOVOS MÉTODOS PARA GERENCIAR ENDEREÇOS DO CLIENTE
       ========================================================================= */

    // Busca todos os endereços vinculados a este cliente
    public static function buscarEnderecos($cliente_id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM enderecos WHERE cliente_id = ? ORDER BY is_padrao DESC, id ASC");
        $stmt->execute([$cliente_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca o endereço principal (padrão) para preencher a tela de checkout automaticamente
    public static function buscarEnderecoPadrao($cliente_id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM enderecos WHERE cliente_id = ? AND is_padrao = 1 LIMIT 1");
        $stmt->execute([$cliente_id]);
        $endereco = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se ele não tiver um endereço marcado como padrão, pega o primeiro que ele cadastrou
        if (!$endereco) {
            $stmt = $pdo->prepare("SELECT * FROM enderecos WHERE cliente_id = ? LIMIT 1");
            $stmt->execute([$cliente_id]);
            $endereco = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $endereco;
    }

    // Salva um novo endereço extra para o cliente
    public static function salvarEndereco($dados)
    {
        $pdo = Database::connect();

        // Verifica se é o primeiro endereço dele. Se for, já marca como padrão (1).
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM enderecos WHERE cliente_id = ?");
        $stmtCheck->execute([$dados['cliente_id']]);
        $isPadrao = ($stmtCheck->fetchColumn() == 0) ? 1 : 0;

        $sql = "INSERT INTO enderecos (cliente_id, cep, rua, numero, bairro, cidade, estado, complemento, is_padrao) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            $dados['cliente_id'],
            $dados['cep'],
            $dados['rua'],
            $dados['numero'],
            $dados['bairro'],
            $dados['cidade'],
            $dados['estado'],
            $dados['complemento'],
            $isPadrao
        ]);
    }

    // Exclui um endereço específico (Garantindo que pertence ao cliente logado)
    public static function excluirEndereco($endereco_id, $cliente_id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM enderecos WHERE id = ? AND cliente_id = ?");
        return $stmt->execute([$endereco_id, $cliente_id]);
    }
}