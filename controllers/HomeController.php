<?php
class HomeController {
    public function index() {
        // Carrega a view (HTML)
        require __DIR__ . '/../views/home.php';
    }
}