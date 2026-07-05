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
}