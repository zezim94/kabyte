<?php
require_once __DIR__ . '/../config/Database.php';

class Chave
{
    private static $chaves_carregadas = null;

    // Busca todas as chaves de uma vez e guarda na memória
    private static function carregarTodas()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT nome, valor FROM chaves");
        
        self::$chaves_carregadas = [];
        while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
            self::$chaves_carregadas[$linha['nome']] = $linha['valor'];
        }
    }

    // Retorna uma chave específica pelo nome
    public static function get($nome)
    {
        if (self::$chaves_carregadas === null) {
            self::carregarTodas();
        }
        return self::$chaves_carregadas[$nome] ?? null;
    }

    // Retorna todas as chaves do Gemini como um Array (Para o sorteio da API)
    public static function getChavesGemini()
    {
        if (self::$chaves_carregadas === null) {
            self::carregarTodas();
        }
        
        $chaves_gemini = [];
        foreach (self::$chaves_carregadas as $nome => $valor) {
            if (strpos($nome, 'gemini_api_') === 0 && !empty(trim($valor))) {
                $chaves_gemini[] = $valor;
            }
        }
        return $chaves_gemini;
    }

    // Busca todas as chaves completas para exibir no painel Admin
    public static function buscarTodasAdmin()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM chaves ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Atualiza o valor de uma chave específica pelo ID
    public static function atualizar($id, $valor)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE chaves SET valor = ? WHERE id = ?");
        return $stmt->execute([$valor, $id]);
    }
}