<?php
require_once __DIR__ . '/../models/Relatorio.php';
require_once __DIR__ . '/../models/Auth.php';

class RelatorioController
{
    /**
     * Rota: index.php?rota=relatorio/index (ou apenas index.php?rota=relatorio)
     * Carrega a página HTML dos relatórios
     */
    public function index()
    {
        Auth::verificar(['admin', 'master']);
        require __DIR__ . '/../views/relatorios/index.php';
    }

    /**
     * Rota: index.php?rota=relatorio/dados
     * Endpoint API para retornar o JSON com os dados filtrados
     */
    public function dados()
    {
        header('Content-Type: application/json');

        // Segurança via sessão do Admin
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['sucesso' => false, 'msg' => 'Acesso negado']);
            return;
        }

        try {
            // 1. Busca os dados no Model
            $vendas = Relatorio::buscarVendasFiltradas($_GET);

            // 2. Processa os cálculos de resumo e gráficos
            $metricas = Relatorio::processarMetricas($vendas);

            // 3. Retorna o JSON estruturado
            echo json_encode([
                'sucesso'  => true,
                'lista'    => $metricas['lista'],
                'resumo'   => $metricas['resumo'],
                'graficos' => $metricas['graficos']
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'sucesso' => false, 
                'msg'     => 'Erro ao gerar relatório: ' . $e->getMessage()
            ]);
        }
    }
}