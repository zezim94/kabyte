<?php
class Seguranca
{
    // Crie uma senha forte e NUNCA a perca, senão não consegue ler os CPFs antigos!
    private static $chave = 'KaByte_Chave_Super_Secreta_2026';
    private static $metodo = 'aes-256-cbc';

    // O "IV" (Vetor de Inicialização) precisa ter exatamente 16 caracteres.
    // Usar um IV fixo é o que permite pesquisar o CPF depois.
    private static $iv = '1234567890123456';

    public static function encriptar($dado)
    {
        if (empty($dado)) return $dado;

        // 1. Limpa a formatação (remove pontos e traços) para garantir que 
        // "123.456" e "123456" gerem a mesma criptografia.
        $dadoLimpo = preg_replace('/[^0-9]/', '', $dado);

        // 2. Criptografa o dado
        return openssl_encrypt($dadoLimpo, self::$metodo, self::$chave, 0, self::$iv);
    }

    public static function descriptografar($dadoCriptografado)
    {
        if (empty($dadoCriptografado)) return $dadoCriptografado;

        // Destranca o dado para ser lido no PHP
        $dadoAberto = openssl_decrypt($dadoCriptografado, self::$metodo, self::$chave, 0, self::$iv);

        return $dadoAberto;
    }
}
