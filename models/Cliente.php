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

    public static function pesquisarPorTermo($termo)
    {
        $pdo = Database::connect();

        $termoFormatado = "%{$termo}%";

        $sql = "SELECT id, nome, cpf FROM clientes WHERE nome LIKE ? OR cpf LIKE ? LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$termoFormatado, $termoFormatado]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public static function buscarPorLogin($login)
    {
        $pdo = Database::connect();

        $sql = 'SELECT * FROM clientes WHERE (cpf = ? OR email = ? OR telefone = ?) LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$login, $login, $login]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function salvar($dados)
    {
        $pdo = Database::connect();

        try {
            // Inicia a transação (se uma falhar, cancela tudo)
            $pdo->beginTransaction();

            // 1. Salva os dados na tabela CLIENTES
            $sqlCliente = "INSERT INTO clientes (nome, cpf, telefone, email) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sqlCliente);
            $stmt->execute([
                $dados['nome'] ?? null,
                $dados['cpf'] ?? null,
                $dados['telefone'] ?? null,
                $dados['email'] ?? null
            ]);

            // Pega o ID do cliente recém-criado
            $clienteId = $pdo->lastInsertId();

            // 2. Salva os dados na tabela ENDERECOS (se o CEP foi preenchido)
            if (!empty($dados['cep'])) {
                // COLUNA CORRIGIDA PARA is_padrao
                $sqlEndereco = "INSERT INTO enderecos (cliente_id, rua, numero, bairro, cidade, estado, cep, complemento, is_padrao) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
                $stmtEnd = $pdo->prepare($sqlEndereco);
                $stmtEnd->execute([
                    $clienteId,
                    $dados['rua'] ?? null,
                    $dados['numero'] ?? null,
                    $dados['bairro'] ?? null,
                    $dados['cidade'] ?? null,
                    $dados['estado'] ?? null,
                    $dados['cep'] ?? null,
                    $dados['complemento'] ?? null
                ]);
            }

            // Confirma as inserções no banco
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack(); // Desfaz a criação do cliente se o endereço falhar
            error_log("Erro ao salvar cliente: " . $e->getMessage());
            return false;
        }
    }

    public static function atualizar($id, $dados)
    {
        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();

            // 1. Atualiza os dados na tabela CLIENTES
            $sqlCliente = "UPDATE clientes SET nome = ?, cpf = ?, telefone = ?, email = ? WHERE id = ?";
            $stmt = $pdo->prepare($sqlCliente);
            $stmt->execute([
                $dados['nome'] ?? null,
                $dados['cpf'] ?? null,
                $dados['telefone'] ?? null,
                $dados['email'] ?? null,
                $id
            ]);

            // 2. Trata a tabela ENDERECOS
            if (!empty($dados['cep'])) {

                // Verifica se já tem um ID de endereço (veio do input hidden do formulário)
                if (!empty($dados['endereco_id'])) {
                    // Já existia endereço, então FAZ UPDATE
                    $sqlEndereco = "UPDATE enderecos SET rua = ?, numero = ?, bairro = ?, cidade = ?, estado = ?, cep = ?, complemento = ? WHERE id = ?";
                    $stmtEnd = $pdo->prepare($sqlEndereco);
                    $stmtEnd->execute([
                        $dados['rua'] ?? null,
                        $dados['numero'] ?? null,
                        $dados['bairro'] ?? null,
                        $dados['cidade'] ?? null,
                        $dados['estado'] ?? null,
                        $dados['cep'] ?? null,
                        $dados['complemento'] ?? null,
                        $dados['endereco_id']
                    ]);
                } else {
                    // Não tinha endereço antes, mas agora preencheu, então FAZ INSERT
                    // COLUNA CORRIGIDA PARA is_padrao
                    $sqlEndereco = "INSERT INTO enderecos (cliente_id, rua, numero, bairro, cidade, estado, cep, complemento, is_padrao) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
                    $stmtEnd = $pdo->prepare($sqlEndereco);
                    $stmtEnd->execute([
                        $id,
                        $dados['rua'] ?? null,
                        $dados['numero'] ?? null,
                        $dados['bairro'] ?? null,
                        $dados['cidade'] ?? null,
                        $dados['estado'] ?? null,
                        $dados['cep'] ?? null,
                        $dados['complemento'] ?? null
                    ]);
                }
            }

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erro ao atualizar cliente: " . $e->getMessage());
            return false;
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

    public static function tornarEnderecoPadrao($endereco_id, $cliente_id)
    {
        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();

            // 1. Remove a flag 'is_padrao = 1' de todos os endereços do cliente
            $stmt1 = $pdo->prepare("UPDATE enderecos SET is_padrao = 0 WHERE cliente_id = ?");
            $stmt1->execute([$cliente_id]);

            // 2. Coloca a flag 'is_padrao = 1' apenas no endereço escolhido
            $stmt2 = $pdo->prepare("UPDATE enderecos SET is_padrao = 1 WHERE id = ? AND cliente_id = ?");
            $stmt2->execute([$endereco_id, $cliente_id]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
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

    // Verifica se o CPF já está cadastrado
    public static function buscarPorCpf($cpf)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE cpf = ? LIMIT 1");
        $stmt->execute([$cpf]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Registra um cliente novo pelo formulário do site (Vitrine)
    public static function registrarNovoCliente($dados)
    {
        try {
            $pdo = Database::connect();

            $sql = "INSERT INTO clientes (nome, email, telefone, cpf, senha, data_cadastro) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);

            $sucesso = $stmt->execute([
                $dados['nome'],
                $dados['email'],
                $dados['telefone'],
                $dados['cpf'],
                $dados['senha']
            ]);

            if ($sucesso) {
                return $pdo->lastInsertId();
            } else {
                // SEGREDO REVELADO: Aqui pegamos o erro silencioso do MySQL!
                $erroMySQL = $stmt->errorInfo();
                die("ERRO ESCONDIDO DO MYSQL: " . print_r($erroMySQL, true));
            }
        } catch (Throwable $e) {
            die("Erro crítico (Exceção): " . $e->getMessage());
        }
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
