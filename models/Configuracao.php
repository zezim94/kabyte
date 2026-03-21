<?php
require_once __DIR__ . '/../config/Database.php';

class Configuracao
{

    // Busca as configurações
    public static function get()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM configuracoes WHERE id = 1");
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retorna dados do banco ou array padrão
        return $dados ? $dados : [
            'nome_loja' => 'KaByte', // Atualizado para o nome da sua loja
            'logo_loja' => '',
            'whatsapp' => '5511999999999',
            'cor_header' => '#2c3e50',
            'cor_fundo' => '#f4f7f6',
            'banners' => '[]' // Novo campo padrão (JSON vazio)
        ];
    }

    // Salva as alterações (Incluindo Logo e Banners)
    public static function salvar($dados)
    {
        $pdo = Database::connect();

        // Adicionamos logo_loja e banners na query SQL
        $sql = "UPDATE configuracoes SET nome_loja=?, whatsapp=?, cor_header=?, cor_fundo=?, logo_loja=?, banners=? WHERE id=1";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            $dados['nome'],
            $dados['whatsapp'],
            $dados['cor_header'],
            $dados['cor_fundo'],
            $dados['logo'],    // Caminho da imagem da logo
            $dados['banners']  // Lista de imagens do banner em formato JSON
        ]);
    }
}