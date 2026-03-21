<?php
require_once __DIR__ . '/../config/Database.php';

class Categoria
{
    public static function listar()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function salvar($dados)
    {
        $pdo = Database::connect();

        // Validação simples
        if (empty($dados['nome'])) {
            return ['sucesso' => false, 'msg' => 'Nome é obrigatório.'];
        }

        try {
            if (empty($dados['id'])) {
                // INSERIR
                $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
                $stmt->execute([$dados['nome']]);
                return ['sucesso' => true, 'msg' => 'Categoria criada com sucesso!'];
            } else {
                // ATUALIZAR
                $stmt = $pdo->prepare("UPDATE categorias SET nome = ? WHERE id = ?");
                $stmt->execute([$dados['nome'], $dados['id']]);
                return ['sucesso' => true, 'msg' => 'Categoria atualizada com sucesso!'];
            }
        } catch (PDOException $e) {
            return ['sucesso' => false, 'msg' => 'Erro no banco: ' . $e->getMessage()];
        }
    }

    public static function excluir($id)
    {
        $pdo = Database::connect();
        try {
            // Verifica se tem produtos usando essa categoria antes de apagar
            $check = $pdo->prepare("SELECT COUNT(*) as total FROM produtos WHERE categoria_id = ?");
            $check->execute([$id]);
            if ($check->fetch()['total'] > 0) {
                return ['sucesso' => false, 'msg' => 'Não é possível excluir: Existem produtos nesta categoria.'];
            }

            $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
            $stmt->execute([$id]);
            return ['sucesso' => true, 'msg' => 'Categoria excluída.'];
        } catch (PDOException $e) {
            return ['sucesso' => false, 'msg' => 'Erro ao excluir: ' . $e->getMessage()];
        }
    }
}