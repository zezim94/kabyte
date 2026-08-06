<?php
class HomeController
{


    public $livre = [
        'sobre',
        'troca',
        'privacidade'
    ];
    public function index()
    {
        // Carrega a view (HTML)
        require __DIR__ . '/../views/home.php';
    }

    // Página Sobre a Empresa (Pública)
    public function sobre()
    {
        // Como é uma página estática simples, só precisamos puxar a view
        require __DIR__ . '/../views/sobre.php';
    }

    public function troca()
    {
        // Como é uma página estática simples, só precisamos puxar a view
        require __DIR__ . '/../views/politica-trocas.php';
    }

    public function privacidade()
    {
        // Como é uma página estática simples, só precisamos puxar a view
        require __DIR__ . '/../views/privacidade.php';
    }
}
