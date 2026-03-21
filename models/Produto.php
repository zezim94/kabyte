<?php
require_once __DIR__ . '/../config/Database.php';

class Produto
{
    // Buscar todos (para a lista)
    public static function listar()
    {
        $pdo = Database::connect();
        $sql = "SELECT p.*, c.nome as categoria_nome 
                FROM produtos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                ORDER BY p.nome ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar relacionados
    public static function listarRelacionados($categoriaId, $excluirId, $limite = 4)
    {
        $pdo = Database::connect();
        $sql = "SELECT * FROM produtos 
                WHERE categoria_id = ? AND id != ? 
                ORDER BY RAND() LIMIT $limite";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$categoriaId, $excluirId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar apenas um
    public static function buscarPorId($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar por código
    public static function buscarPorCodigo($codigo)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE codigo_barras = ?");
        $stmt->execute([$codigo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verifica existência
    public static function existe($codigo)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT id FROM produtos WHERE codigo_barras = ?");
        $stmt->execute([$codigo]);
        return $stmt->rowCount() > 0;
    }

    // Busca para a VITRINE (AQUI ESTAVA O PROBLEMA)
    public static function buscarPorTermo($termo)
    {
        $pdo = Database::connect();

        // O % serve para buscar em qualquer parte do texto
        $termoFormatado = "%{$termo}%";

        // Removi 'AND ativo = 1' pois sua tabela parece não ter essa coluna ainda
        $sql = "SELECT * FROM produtos 
                WHERE (nome LIKE :termo OR descricao LIKE :termo) 
                AND estoque > 0";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':termo', $termoFormatado);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Salvar novo
    public static function salvar($dados)
    {
        $pdo = Database::connect();
        $sql = "INSERT INTO produtos (codigo_barras, nome, descricao, preco, preco_custo, promocao, preco_promocao, estoque, categoria_id, imagem) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $sucesso = $stmt->execute([
            $dados['codigo'],
            $dados['nome'],
            $dados['descricao'],
            $dados['preco'],
            $dados['preco_custo'],
            $dados['promocao'],
            $dados['preco_promocao'],
            $dados['estoque'],
            $dados['categoria_id'],
            $dados['imagem']
        ]);

        // Retorna o ID gerado para podermos vincular as fotos extras
        return $sucesso ? $pdo->lastInsertId() : false;
    }

    // Busca produtos filtrados por Categoria
    public static function buscarPorCategoria($categoriaId)
    {
        $pdo = Database::connect();
        $sql = "SELECT * FROM produtos 
                WHERE categoria_id = ? 
                AND estoque > 0";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$categoriaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Atualizar existente
    public static function atualizar($id, $dados)
    {
        $pdo = Database::connect();

        $sqlBase = "codigo_barras=?, nome=?, descricao=?, preco=?, preco_custo=?, promocao=?, preco_promocao=?, estoque=?, categoria_id=?";

        $params = [
            $dados['codigo'],
            $dados['nome'],
            $dados['descricao'],
            $dados['preco'],
            $dados['preco_custo'],
            $dados['promocao'],
            $dados['preco_promocao'],
            $dados['estoque'],
            $dados['categoria_id']
        ];

        if (!empty($dados['imagem'])) {
            $sql = "UPDATE produtos SET $sqlBase, imagem=? WHERE id=?";
            $params[] = $dados['imagem'];
        } else {
            $sql = "UPDATE produtos SET $sqlBase WHERE id=?";
        }

        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    // Excluir
    public static function excluir($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Busca as imagens extras de um produto para a galeria
    public static function buscarImagensAdicionais($produto_id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM produto_imagens WHERE produto_id = ?");
        $stmt->execute([$produto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Grava uma foto extra no banco
    public static function salvarImagemExtra($produto_id, $nomeImagem)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO produto_imagens (produto_id, imagem) VALUES (?, ?)");
        return $stmt->execute([$produto_id, $nomeImagem]);
    }

    // Busca a informação de UMA foto extra para o momento da exclusão
    public static function buscarImagemExtraPorId($imagem_id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM produto_imagens WHERE id = ?");
        $stmt->execute([$imagem_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Apaga UMA foto extra do banco
    public static function excluirImagemExtra($imagem_id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM produto_imagens WHERE id = ?");
        return $stmt->execute([$imagem_id]);
    }
}