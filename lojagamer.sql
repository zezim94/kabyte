-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19-Jun-2026 às 15:38
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `lojagamer`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(1, 'Cabos e Conectividade'),
(2, 'Mouse'),
(3, 'Teclado'),
(4, 'Fone'),
(5, 'Iluminação e Decoração Tech'),
(6, 'Armazenamento'),
(7, 'Acessórios Extras'),
(8, 'Smartwatch'),
(9, 'Monitor');

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `senha` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `cpf`, `telefone`, `email`, `google_id`, `foto`, `data_cadastro`, `senha`) VALUES
(1, 'Ester vitória Moura da Silva', '12345678901', '(13) 97819-0027', 'ester@gmail.com', NULL, NULL, '2025-12-26 19:59:08', '$2y$10$YdzMee5sCI1Rvfa27JoxreidzKCWdfwiIABn5bFI1XR/jwiykiXtm'),
(2, 'Andelson nascimento', '11098717678', '(13) 97819-0027', 'geovanesilva301@gmail.com', '112725934260905830548', 'https://lh3.googleusercontent.com/a/ACg8ocLj7pL49wvcEq51avYMRBIc-PFcnFqgUdzaF8cqDkhnTJOXrk_xWA=s96-c', '2026-01-24 15:27:32', NULL),
(3, 'Sarah Stephanie', NULL, NULL, 'stephaniemoura285@gmail.com', '118063862504631323329', 'https://lh3.googleusercontent.com/a/ACg8ocKM2vBoB7D7TgembzR4xIlfZye6MHuIJlSmovqmhQNfnD4myk9n=s96-c', '2026-02-05 14:02:10', NULL),
(4, 'Andelson Nascimento', '11098717670', '13978190027', 'andelsonascimento@gmail.com', '109035660575826182106', 'https://lh3.googleusercontent.com/a/ACg8ocLzPhlA0uMuEugcpNd18R3XujL0KD8i-USXeOC0gcAZGSMs6lAr=s96-c', '2026-03-02 09:44:18', '$2y$10$MgMREzojnH9WQqeUili7eOod8YcEDiEQ4gsMG.bFzRoWc5kwJ7ANy');

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL DEFAULT 1,
  `logo_loja` varchar(100) DEFAULT 'Bicicletaria Paty Bike',
  `whatsapp` varchar(20) DEFAULT '5511999999999',
  `cor_header` varchar(20) DEFAULT '#2c3e50',
  `cor_fundo` varchar(20) DEFAULT '#f4f7f6',
  `nome_loja` varchar(255) DEFAULT NULL,
  `banners` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `logo_loja`, `whatsapp`, `cor_header`, `cor_fundo`, `nome_loja`, `banners`) VALUES
(1, 'public/uploads/logo_loja.png?v=1774108180', '5511999999999', '#1f3a5f', '#f5f5f5', 'Kabyte', '[\"banner_20260321163744_69bebb487e967.jpg\",\"banner_20260321163744_69bebb487ed4f.jpg\",\"banner_20260321163744_69bebb487f02e.jpg\",\"banner_20260321163744_69bebb487f2e4.jpeg\"]');

-- --------------------------------------------------------

--
-- Estrutura da tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `is_padrao` tinyint(1) DEFAULT 0 COMMENT '1 para Sim, 0 para Não'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `enderecos`
--

INSERT INTO `enderecos` (`id`, `cliente_id`, `rua`, `numero`, `bairro`, `cidade`, `estado`, `cep`, `complemento`, `is_padrao`) VALUES
(1, 4, 'Avenida Ferroviária', '81', 'Vila dos Pescadores', 'Cubatão', 'SP', '11531000', 'caminho são gabriel', 0),
(2, 4, 'Rua Benedito Aires', '120', 'Vila Paulista', 'Cubatão', 'SP', '11510120', 'centro', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `itens_venda`
--

CREATE TABLE `itens_venda` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `itens_venda`
--

INSERT INTO `itens_venda` (`id`, `venda_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(22, 11, 2, 1, 24.90),
(23, 11, 4, 1, 12.00),
(28, 14, 1, 3, 10.50),
(29, 14, 3, 4, 0.80),
(30, 15, 2, 2, 24.90),
(31, 16, 1, 3, 10.50),
(32, 16, 2, 2, 24.90),
(33, 17, 2, 1, 24.90),
(34, 17, 7, 1, 3.00),
(35, 20, 2, 1, 24.90),
(36, 20, 4, 1, 12.00),
(37, 20, 1, 1, 10.50),
(38, 21, 2, 1, 24.90),
(39, 22, 7, 1, 3.00),
(40, 23, 2, 1, 24.90),
(41, 23, 3, 15, 0.80),
(42, 24, 1, 10, 9.95),
(43, 24, 2, 1, 24.90),
(44, 24, 3, 11, 0.80),
(45, 24, 4, 2, 12.00),
(46, 25, 1, 1, 9.95),
(47, 25, 7, 2, 3.00),
(48, 30, 1, 1, 9.95),
(49, 30, 2, 1, 24.90),
(50, 30, 4, 1, 12.00),
(51, 30, 7, 1, 3.00),
(52, 31, 2, 1, 24.90),
(53, 31, 4, 1, 12.00),
(54, 32, 2, 1, 24.90),
(55, 32, 7, 1, 3.00),
(56, 34, 1, 1, 9.95),
(57, 34, 2, 1, 24.90),
(58, 34, 3, 10, 0.80),
(59, 34, 4, 1, 12.00),
(60, 34, 7, 1, 3.00),
(61, 35, 3, 2, 0.80);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `mp_id` varchar(50) NOT NULL COMMENT 'ID da transação no Mercado Pago',
  `status` varchar(20) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `transaction_details` text DEFAULT NULL COMMENT 'JSON completo do retorno para auditoria',
  `valor` decimal(10,2) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `payment_type` varchar(50) DEFAULT NULL COMMENT 'credit_card, ticket, pix',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'master, visa, pix, bolbradesco',
  `installments` int(11) DEFAULT 1,
  `installment_value` decimal(10,2) DEFAULT NULL,
  `total_paid` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `pagamentos`
--

INSERT INTO `pagamentos` (`id`, `venda_id`, `cliente_id`, `mp_id`, `status`, `descricao`, `transaction_details`, `valor`, `data_criacao`, `payment_type`, `payment_method`, `installments`, `installment_value`, `total_paid`) VALUES
(1, 11, 1, '1325750238', 'approved', 'Pedido #11', '{\"id\":1325750238,\"date_created\":\"2025-12-27T11:32:24.495-04:00\",\"date_approved\":\"2025-12-27T11:32:24.922-04:00\",\"date_last_updated\":\"2025-12-27T11:32:24.922-04:00\",\"date_of_expiration\":null,\"money_release_date\":\"2025-12-27T11:32:24.922-04:00\",\"money_release_status\":\"released\",\"operation_type\":\"regular_payment\",\"issuer_id\":\"24\",\"payment_method_id\":\"master\",\"payment_type_id\":\"credit_card\",\"payment_method\":{\"id\":\"master\",\"type\":\"credit_card\",\"issuer_id\":\"24\",\"data\":{\"routing_data\":{\"merchant_account_id\":\"103\"}}},\"status\":\"approved\",\"status_detail\":\"accredited\",\"currency_id\":\"BRL\",\"description\":\"Pedido #11\",\"live_mode\":false,\"sponsor_id\":null,\"authorization_code\":\"301299\",\"money_release_schema\":null,\"taxes_amount\":0,\"counter_currency\":null,\"brand_id\":null,\"shipping_amount\":0,\"build_version\":\"3.136.0-rc-1\",\"pos_id\":null,\"store_id\":null,\"integrator_id\":null,\"platform_id\":null,\"corporation_id\":null,\"charges_execution_info\":{\"internal_execution\":{\"date\":\"2025-12-27T11:32:24.472-04:00\",\"execution_id\":\"01KDG7107HWJ73FFVGP34SJ6DC\"}},\"payer\":{\"identification\":{\"number\":\"32659430\",\"type\":\"DNI\"},\"entity_type\":null,\"phone\":{\"number\":null,\"extension\":null,\"area_code\":null},\"last_name\":null,\"id\":\"3096780438\",\"type\":null,\"first_name\":null,\"email\":\"test_user_80507629@testuser.com\"},\"collector_id\":198821613,\"marketplace_owner\":null,\"metadata\":[],\"additional_info\":{\"nsu_processadora\":\"30121999\",\"tracking_id\":\"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none\"},\"order\":[],\"external_reference\":null,\"transaction_amount\":11.9,\"transaction_amount_refunded\":0,\"coupon_amount\":0,\"differential_pricing_id\":null,\"financing_group\":null,\"deduction_schema\":null,\"installments\":1,\"transaction_details\":{\"payment_method_reference_id\":null,\"acquirer_reference\":null,\"net_received_amount\":11.42,\"total_paid_amount\":11.9,\"overpaid_amount\":0,\"external_resource_url\":null,\"installment_amount\":11.9,\"financial_institution\":null,\"payable_deferral_period\":null},\"fee_details\":[{\"type\":\"mercadopago_fee\",\"amount\":0.36,\"fee_payer\":\"collector\"}],\"charges_details\":[{\"id\":\"1325750238-001\",\"name\":\"mercadopago_fee\",\"type\":\"fee\",\"accounts\":{\"from\":\"collector\",\"to\":\"mp\"},\"client_id\":0,\"date_created\":\"2025-12-27T11:32:24.497-04:00\",\"last_updated\":\"2025-12-27T11:32:24.497-04:00\",\"amounts\":{\"original\":0.36,\"refunded\":0},\"metadata\":{\"source\":\"rule-engine\",\"source_detail\":\"processing_fee_charge\",\"reason\":\"\"},\"reserve_id\":null,\"refund_charges\":[],\"update_charges\":[]},{\"id\":\"1325750238-002\",\"name\":\"financed_advance_fee\",\"type\":\"fee\",\"accounts\":{\"from\":\"collector\",\"to\":\"mc\"},\"client_id\":0,\"date_created\":\"2025-12-27T11:32:24.497-04:00\",\"last_updated\":\"2025-12-27T11:32:24.497-04:00\",\"amounts\":{\"original\":0.12,\"refunded\":0},\"metadata\":{\"source\":\"rule-engine\",\"source_detail\":\"financed_advance_fee_charge\"},\"reserve_id\":null,\"refund_charges\":[],\"rate\":1,\"base_amount\":0,\"update_charges\":[]}],\"captured\":true,\"binary_mode\":false,\"call_for_authorize_id\":null,\"statement_descriptor\":\"Mercadopago*fake\",\"card\":{\"id\":null,\"first_six_digits\":\"503143\",\"last_four_digits\":\"6351\",\"expiration_month\":11,\"expiration_year\":2030,\"date_created\":\"2025-12-27T11:32:24.000-04:00\",\"date_last_updated\":\"2025-12-27T11:32:24.000-04:00\",\"country\":null,\"tags\":[\"credit\"],\"cardholder\":{\"name\":\"APRO\",\"identification\":{\"number\":\"12345678909\",\"type\":\"CPF\"}}},\"notification_url\":null,\"refunds\":[],\"processing_mode\":\"aggregator\",\"merchant_account_id\":null,\"merchant_number\":null,\"acquirer_reconciliation\":[],\"point_of_interaction\":{\"type\":\"UNSPECIFIED\",\"business_info\":{\"unit\":\"online_payments\",\"sub_unit\":\"default\",\"branch\":\"Merchant Services\"}},\"accounts_info\":null,\"release_info\":{\"advance_provider\":\"mc\",\"advance_provider_user\":null,\"events\":null,\"new_payments_release_model\":false},\"tags\":[\"release_advance_by_mc\"]}', 11.90, '2025-12-27 12:32:31', 'credit_card', 'master', 1, 11.90, 11.90),
(2, 14, 2, '1344170781', 'approved', NULL, NULL, 34.70, '2026-01-24 23:16:58', NULL, 'master', 1, NULL, NULL),
(3, 15, 2, '1344174281', 'pending', 'Pedido Web - Cliente #2', '{\"payment_method_reference_id\":null,\"acquirer_reference\":null,\"net_received_amount\":0,\"total_paid_amount\":49.8,\"overpaid_amount\":0,\"external_resource_url\":null,\"installment_amount\":0,\"financial_institution\":null,\"payable_deferral_period\":null,\"bank_transfer_id\":null,\"transaction_id\":null}', 49.80, '2026-01-24 23:22:02', 'bank_transfer', 'pix', 1, 0.00, 49.80),
(4, 16, 2, '1344174333', 'pending', 'Pedido Web - Cliente #2', '{\"payment_method_reference_id\":null,\"acquirer_reference\":null,\"net_received_amount\":0,\"total_paid_amount\":81.3,\"overpaid_amount\":0,\"external_resource_url\":null,\"installment_amount\":0,\"financial_institution\":null,\"payable_deferral_period\":null,\"bank_transfer_id\":null,\"transaction_id\":null}', 81.30, '2026-01-24 23:31:55', 'bank_transfer', 'pix', 1, 0.00, 81.30),
(5, 24, 2, '1344262703', 'pending', 'Pedido Web - Cliente #2', '{\"payment_method_reference_id\":null,\"acquirer_reference\":null,\"net_received_amount\":0,\"total_paid_amount\":157.2,\"overpaid_amount\":0,\"external_resource_url\":null,\"installment_amount\":0,\"financial_institution\":null,\"payable_deferral_period\":null,\"bank_transfer_id\":null,\"transaction_id\":null}', 157.20, '2026-01-28 05:26:42', 'bank_transfer', 'pix', 1, 0.00, 157.20),
(6, 25, 2, '1344264213', 'pending', 'Pedido Web - Cliente #2', '{\"payment_method_reference_id\":null,\"acquirer_reference\":null,\"net_received_amount\":0,\"total_paid_amount\":15.95,\"overpaid_amount\":0,\"external_resource_url\":null,\"installment_amount\":0,\"financial_institution\":null,\"payable_deferral_period\":null,\"bank_transfer_id\":null,\"transaction_id\":null}', 15.95, '2026-01-28 05:42:52', 'bank_transfer', 'pix', 1, 0.00, 15.95),
(7, 30, 2, '1344264237', 'approved', 'Pedido Web - Cliente #2', '{\"payment_method_reference_id\":null,\"acquirer_reference\":null,\"net_received_amount\":48.36,\"total_paid_amount\":58.18,\"overpaid_amount\":0,\"external_resource_url\":null,\"installment_amount\":8.31,\"financial_institution\":null,\"payable_deferral_period\":null}', 49.85, '2026-01-28 05:58:25', 'credit_card', 'master', 7, 8.31, 58.18),
(8, 31, 2, '1344262805', 'pending', 'Pedido Web - Cliente #2', '{\"payment_method_reference_id\":null,\"acquirer_reference\":null,\"net_received_amount\":0,\"total_paid_amount\":36.9,\"overpaid_amount\":0,\"external_resource_url\":null,\"installment_amount\":0,\"financial_institution\":null,\"payable_deferral_period\":null,\"bank_transfer_id\":null,\"transaction_id\":null}', 36.90, '2026-01-28 06:02:28', 'bank_transfer', 'pix', 1, 0.00, 36.90),
(9, 32, 2, '1344262809', 'approved', 'Pedido Web - Cliente #2', '{\"payment_method_reference_id\":null,\"acquirer_reference\":null,\"net_received_amount\":26.79,\"total_paid_amount\":27.9,\"overpaid_amount\":0,\"external_resource_url\":null,\"installment_amount\":27.9,\"financial_institution\":null,\"payable_deferral_period\":null}', 27.90, '2026-01-28 06:04:36', 'credit_card', 'master', 1, 27.90, 27.90),
(10, 34, 2, '1344264265', 'pending', 'Pedido Web - Cliente #2', '{\"payment_method_reference_id\":null,\"acquirer_reference\":null,\"net_received_amount\":0,\"total_paid_amount\":57.85,\"overpaid_amount\":0,\"external_resource_url\":null,\"installment_amount\":0,\"financial_institution\":null,\"payable_deferral_period\":null,\"bank_transfer_id\":null,\"transaction_id\":null}', 57.85, '2026-01-28 06:07:25', 'bank_transfer', 'pix', 1, 0.00, 57.85),
(11, 35, 4, '1326450484', 'pending', 'Pedido Web - Cliente #4', '{\"payment_method_reference_id\":null,\"acquirer_reference\":null,\"net_received_amount\":0,\"total_paid_amount\":1.600000000000000088817841970012523233890533447265625,\"overpaid_amount\":0,\"external_resource_url\":null,\"installment_amount\":0,\"financial_institution\":null,\"payable_deferral_period\":null,\"bank_transfer_id\":null,\"transaction_id\":null}', 1.60, '2026-03-02 09:44:55', 'bank_transfer', 'pix', 1, 0.00, 1.60);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `codigo_barras` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_custo` decimal(10,2) DEFAULT 0.00,
  `promocao` tinyint(1) DEFAULT 0,
  `preco_promocao` decimal(10,2) DEFAULT NULL,
  `estoque` int(11) NOT NULL DEFAULT 0,
  `imagem` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `codigo_barras`, `nome`, `descricao`, `preco`, `preco_custo`, `promocao`, `preco_promocao`, `estoque`, `imagem`, `categoria_id`) VALUES
(1, '7891000100103', 'Carregador duplo', 'Original', 10.50, 8.00, 0, NULL, 75, 'prod_20260320205711_69bda697623ee.png', 1),
(2, '7891000053508', 'C3Tech Base para Notebook', 'NBC-50BK 15,6\" Refrigerada com 2 Coolers, Led de Iluminacao Azul, Inclinacao Ajustável em 5 niveis, com USB, cor Preta', 70.00, 35.00, 1, 49.99, 23, 'prod_20260321163859_69bebb939eea1.png', 7),
(3, '123', 'Fita Led 5M', 'Fita LED com 5 metros, econômica e flexível, ideal para decoração de ambientes internos e externos. Possui proteção IP65, sendo resistente à água e poeira, garantindo durabilidade e versatilidade na instalação.', 45.00, 20.00, 1, 30.00, 46, 'prod_20260321164112_69bebc1870c8e.png', 5),
(4, '111', 'Cabo HDMI', 'Cabo HDMI de 10 metros, ideal para transmissão de áudio e vídeo em alta definição. Suporta até 1080p Full HD, possui conexão estável, plug and play e conectores reforçados para maior durabilidade. Compatível com TVs, videogames e outros dispositivos HDMI. Garantia de 30 dias.', 25.00, 10.00, 1, 15.99, 15, 'prod_20260321165712_69bebfd8b26ef.png', 1),
(7, '321456', 'Cabo USB tipo C Intelbras', 'Esse cabo é ideal para conectar seus dispositivos com facilidade e simplicidade. Dessa forma, você pode transferir dados em apenas alguns passos e de maneira segura e confiável.\r\n\r\n', 20.00, 5.00, 0, NULL, 43, 'prod_20260320205641_69bda6790903a.png', 1),
(8, '3214', 'Fone De Ouvido Tws Wf200', 'Fone TWS Pulse WF200, com Bluetooth 5.4 e som estéreo de alta qualidade. Possui até 22h de bateria com a base, alcance de 10m e carregamento rápido via USB-C. Compacto, leve e ideal para uso diário.', 80.00, 40.00, 0, NULL, 12, 'prod_20260320205611_69bda65b7aab3.png', 4),
(9, '789', 'Headset Gamer Havit Gamenote', 'Headset HAVIT H659d, com som de alta qualidade (drivers de 50mm) e microfone omnidirecional flexível. Possui conexão P3 (3,5mm), cabo de 2,3m e design confortável com ajuste de tamanho. Compatível com celular, PC e consoles.', 120.00, 50.00, 0, NULL, 50, 'prod_20260321164251_69bebc7b069e7.png', 4),
(10, '6548', 'HD Externo 1TB SanDisk', 'HD externo Exbom HDE-S1TA com 1TB, conexão USB 3.1 e velocidades de até 120 MB/s. Compacto (2.5”), leve e resistente à poeira, ideal para backups e armazenamento de dados. Acompanha cabo USB-C para USB-A e é compatível com PC, TV e consoles.', 320.00, 150.00, 1, 280.00, 50, 'prod_20260321173115_69bec7d39743c.png', 6),
(11, '65479', 'Headset Gamer P2 Power Bass', 'Headset gamer ELG HGAR, com drivers de 40mm e som surround para maior imersão. Possui microfone omnidirecional, design confortável over-ear e iluminação LED verde (via USB). Conexão P2 (fone e mic), cabo de 2m e adaptador para consoles. Garantia de 2 anos.', 250.00, 100.00, 0, NULL, 30, 'prod_20260321164332_69bebca4e949d.png', 4),
(12, '2354643', 'Fone Headset Ms Jabra Du', 'Headset Jabra Evolve 30 II Duo MS, com conexão USB e P2 (3,5mm), ideal para chamadas e uso profissional. Possui áudio em banda larga, controle de chamadas no cabo e instalação plug-and-play. Compatível com plataformas como Microsoft Teams, Avaya e Cisco.', 470.00, 180.00, 1, 400.00, 30, 'prod_20260321164210_69bebc529c681.png', 4),
(13, '2356543', 'Hub USB 4 portas', 'Hub USB X-CELL XC-HUB-6 com 4 portas USB 2.0, ideal para expandir conexões. Possui transferência de até 480 Mbps, sistema plug-and-play e compatibilidade com Windows e Mac. Cabo de 45cm e design compacto na cor preta.', 20.00, 10.00, 0, NULL, 54, 'prod_20260320205945_69bda7316fc2a.png', 1),
(14, '456765', 'Luminária LED para Setup', '', 54.00, 34.00, 0, NULL, 23, 'prod_20260321164643_69bebd6395de1.png', 5),
(15, '346865', 'Luminária Minimalista para Mesa', 'Luminária de mesa Lumicentro Memphis, com design moderno em metal na cor preta. Possui soquete E27, estrutura resistente e base estável, ideal para iluminar e decorar mesas e escritórios com estilo.', 50.00, 25.00, 0, NULL, 34, 'prod_20260321172251_69bec5db5a19f.png', 5),
(16, '2335457', 'Luminária Para Monitor Led Screen Pcyes 5 Níveis De Calor ', 'Luminária LED para monitor PCYES ScreenBar, com 5 níveis de brilho e temperatura de cor ajustável. Instalação sobre o monitor, economizando espaço. Alimentação via USB, controle touch e vida útil de até 25.000 horas. Ideal para reduzir fadiga visual. Garantia de 12 meses.', 200.00, 100.00, 0, NULL, 34, 'prod_20260321164612_69bebd4487587.png', 5),
(17, '345667', 'Mouse Ergonômico', 'Mouse ergonômico vertical Logitech Lift, com design de 57° para maior conforto e redução de esforço. Possui cliques silenciosos, roda SmartWheel e 4 botões personalizáveis. Conecta até 3 dispositivos via Bluetooth ou USB e tem bateria de até 2 anos. Compatível com diversos sistemas operacionais.', 295.00, 160.00, 1, 235.00, 32, 'prod_20260320205816_69bda6d8ed9fd.png', 2),
(18, '25481', 'Mouse Gamer 7200 DPI', 'Mouse gamer War Shark G-90, com sensor óptico e DPI ajustável até 7200. Possui 8 botões, design ergonômico e cabo de 1,8m, ideal para jogos com precisão e conforto.', 60.00, 30.00, 0, NULL, 32, 'prod_20260321171443_69bec3f34977f.png', 2),
(19, '21458', 'Mouse Gamer Optico Usb Flatshop', 'Mouse óptico FlatShop S1 com conexão USB e 1200 DPI, garantindo boa precisão e controle. Possui design ambidestro, 3 botões e iluminação RGB. Compatível com Windows e Linux, ideal para uso diário e jogos leves.', 40.00, 15.00, 0, NULL, 32, 'prod_20260321171412_69bec3d4063b6.png', 2),
(20, '10254', 'Mouse Óptico K-Mex Com Fio', 'Mouse K-Mex MO-M235, com sensor óptico preciso e design ergonômico, leve e compacto. Ideal para uso confortável no dia a dia e fácil de transportar.', 25.00, 10.00, 0, NULL, 36, 'prod_20260321171258_69bec38a23887.png', 2),
(21, '14785', 'Mouse Pad Gamer Fortrek', 'Mouse pad gamer Fortrek MPG104 (90x40cm), superfície speed para mais precisão e conforto. Feito em borracha e tecido, com base antiderrapante e 4mm de espessura. Não possui LED nem apoio de pulso.', 55.00, 20.00, 0, NULL, 32, 'prod_20260321171208_69bec358cf900.png', 7),
(22, '145273', 'Pad Mouse Gamer Rgb', 'Mouse pad Letron RGB modelo RS-02 (35x25cm), com borda iluminada em LED. Feito em borracha e tecido, possui 4mm de espessura, sendo ideal para maior conforto e precisão. Garantia de 7 dias.', 125.00, 60.00, 0, NULL, 33, 'prod_20260320210232_69bda7d82da20.png', 7),
(23, '254874', 'Mouse Sem Fio Multilaser', 'Mouse sem fio Multilaser MO277, com conexão 2.4GHz e bateria de lítio recarregável via Micro USB. Possui até 1600 DPI, é leve, compacto e compatível com Windows, Mac e Linux. Garantia de 90 dias.', 75.00, 35.00, 0, NULL, 32, 'prod_20260320210101_69bda77d9ca7f.png', 2),
(24, '012574', 'Organizador de Cabos', 'Organizador de cabos Tecnollev para 7 cabos, ideal para manter fios organizados e acessíveis. Possui instalação com fita dupla face, design compacto e material resistente. Perfeito para mesas e escritórios. Garantia de 7 dias.', 20.00, 5.00, 0, NULL, 32, 'prod_20260320205912_69bda71057c75.png', 7),
(25, '102452', 'Pendrive 64gb Kingston', 'Pendrive Kingston Exodia S com 64GB, conexão USB 3.2, ideal para armazenar e transferir arquivos com rapidez. Compacto, resistente e compatível com Windows, Mac, Linux e Chrome OS.', 80.00, 35.00, 0, NULL, 33, 'prod_20260321171006_69bec2ded47de.png', 6),
(26, '452178', 'SSD Externo 500GB', 'SSD portátil SanDisk com 500GB, alta velocidade de leitura (até 1050 MB/s) e conexão USB 3.1 Gen 2. Resistente à água e poeira (IP55), quedas de até 2m e impactos. Compacto, leve e ideal para armazenar e transportar dados com segurança. Acompanha cabo USB-C e adaptador USB-A.', 1100.00, 500.00, 0, NULL, 33, 'prod_20260320203919_69bda26749920.png', 6),
(27, '14527', 'Suporte Articulado Monitor Notebook Fortrek', 'Suporte articulado de mesa Fortrek FK485S para monitor (17” a 32”) e notebook (até 15,6”). Suporta até 9kg (monitor) e 4kg (notebook), com rotação e articulação ajustáveis. Compatível com padrão VESA 100x100, fácil de instalar e ideal para melhor ergonomia.', 300.00, 150.00, 0, NULL, 33, 'prod_20260321170252_69bec12c16167.png', 7),
(28, '145278', 'Suporte de Celular de Mesa', 'Suporte de mesa ajustável para celular e tablet, com articulação de até 72º e altura de até 14cm. Possui base estável, design portátil e compatibilidade universal. Feito em plástico, leve e ideal para vídeos e chamadas. Garantia de 3 meses.', 20.00, 10.00, 0, NULL, 33, 'prod_20260320203745_69bda209068f8.png', 7),
(29, '1254563', 'Suporte Para Headset Gamer Redragon', '- Suporte RGB para Headsets - Construção reforçada em Aluminio e plástico ABS militar - Modos de iluminação RGB - 4 Entradas USB 2.0 (2 em compartimento escondido) - Conexão Tipo-C e cabo removivél Peso e dimensões Peso: 267 g Apróx (A) 25CM x (L) 10CM (C) 10CM', 199.99, 90.00, 0, NULL, 33, 'prod_20260320203609_69bda1a949a2f.png', 7),
(30, '1025547', 'Teclado portátil de 60%,', 'Teclado mecânico compacto 60% com 61 teclas, ideal para economizar espaço e transportar. Possui switches vermelhos, anti-ghosting (N-Key Rollover), teclas duráveis em dupla injeção e conexão USB-C removível. Conta com iluminação LED colorida com 19 modos e ajuste de altura para maior conforto.', 210.00, 110.00, 0, NULL, 33, 'prod_20260320203332_69bda10ca4617.png', 3),
(31, '254887', 'Teclado Semi Mecânico', 'Teclado semi mecânico com layout ABNT2, teclas silenciosas e anti-ghost. Possui conexão USB, cabo de 1,5m, design ergonômico e resistência a respingos. Compatível com diversos dispositivos e ideal para uso diário. Garantia de 30 dias.', 50.00, 20.00, 0, NULL, 36, 'prod_20260320203229_69bda0cddc3be.png', 3),
(32, '1478526', 'Teclado Gamer Semi Mecânico', 'Teclado gamer semi mecânico com iluminação LED RGB (7 cores), teclas macias e silenciosas. Possui 107 teclas, conexão USB, design resistente a respingos e impactos. Iluminação pode ser ligada/desligada. Garantia de 3 meses.', 69.90, 30.00, 0, NULL, 35, 'prod_20260320203522_69bda17a2b984.png', 3),
(33, '236988', 'Teclado Pro Fit Media Keyboard Kensington', 'Teclado com fio Kensington Pro Fit (ABNT2), na cor preta, confortável e prático para uso diário. Possui teclado numérico, é resistente a respingos e tem design semimecânico. Não possui retroiluminação nem cabo removível.', 270.00, 150.00, 1, 220.00, 34, 'prod_20260320203301_69bda0ed51d4d.png', 3),
(34, '14875', 'Teclado Portátil Bluetooth 5.0 Ts400', 'Teclado Bluetooth 5.0 sem fio com suporte para celular/tablet. Conecta até 3 dispositivos, possui 14 teclas multimídia e funciona com 2 pilhas AAA (inclusas). Acompanha guia rápido e tem garantia de 3 anos.', 180.00, 100.00, 0, NULL, 36, 'prod_20260320203359_69bda12713e57.png', 3),
(35, '45887558', 'Smartwatch Samsung Galaxy Fit3 Display 1.6\" Grafite', 'Para usar o Galaxy Fit3, é necessário ter uma conta Samsung cadastrada no celular. Os dados de saúde são armazenados com segurança na Samsung Cloud. Se a bateria estiver totalmente descarregada, o dispositivo não ligará e deverá ser carregado antes do uso. A tela sensível ao toque pode não funcionar corretamente quando estiver molhada ou em contato com água.\r\n', 269.91, 100.00, 0, NULL, 20, 'prod_20260320203944_69bda280ef021.png', 8),
(36, '02454552', 'Smartwatch Militar Masculino,Relógio Inteligente com Chamadas,Tela HD de 1.45\"', 'Smartwatch IOWODO W50Pro com tela 1.45\" Full HD, chamadas Bluetooth, notificações de apps, 100 modos esportivos, bateria duradoura, resistência IP68 e funções inteligentes. Acompanha duas pulseiras ajustáveis.\r\n', 299.90, 120.00, 0, NULL, 22, 'prod_20260320204022_69bda2a630b90.png', 8),
(37, '236522', 'Smartwatch com Monitor Cardíaco', 'Smartwatch X Watch XSWUQPI003A PXT com monitor cardíaco, contador de passos e calorias, notificações inteligentes e modos esportivos. Possui tela touch, design moderno e auxilia no acompanhamento da saúde, treinos e atividades do dia a dia.\r\n', 269.99, 100.00, 0, NULL, 21, 'prod_20260320204116_69bda2dc44fe6.png', 8),
(38, '124541225', 'Smartwatch Bluetooth', 'Smartwatch IDW26 BLACKULTRA com tela HD de 1,83\", chamadas Bluetooth, assistente Alexa, monitoramento de saúde (batimentos, sono e oxigênio), modos esportivos, notificações de apps e resistência IP68. Bateria de 5 a 7 dias e compatível com Android e iOS.\r\n', 169.90, 70.00, 0, NULL, 20, 'prod_20260320204138_69bda2f24a210.png', 8),
(39, '1254581', 'Smartwatch AMOLED', 'Smartwatch com GPS de 5 satélites e impermeabilidade 3ATM, ideal para esportes outdoor. Possui mais de 100 modos esportivos, análise de sono com IA, monitoramento de saúde (batimentos, estresse, oxigênio e respiração), chamadas Bluetooth, notificações e mostradores personalizáveis.\r\n', 309.43, 130.00, 1, 250.00, 20, 'prod_20260320204216_69bda31898ae4.png', 8),
(40, '12545822', 'Pulseira Inteligente Xiaomi Smart Band 9 Active', 'Smartband com tela touch de 1,47\", Bluetooth 5.3 e resistência 5 ATM. Possui 50 modos de treino, monitoramento de batimentos cardíacos, oxigênio no sangue, sono e estresse, além de notificações, controle de música e câmera. Bateria de até 18 dias e compatível com Android e iOS.\r\n', 289.99, 120.00, 0, NULL, 30, 'prod_20260321170740_69bec24c05b64.png', 8),
(41, '2011251', 'Carregador Smartwatch Compátivel Microwear', 'Cabo carregador magnético USB para smartwatch Microwear, 1 metro, saída 5V/1A, feito em ABS e metal. Compatível com diversos modelos Microwear com carregamento magnético.\r\n', 23.90, 10.00, 0, NULL, 30, 'prod_20260321164036_69bebbf44ba2f.webp', 8),
(42, '10225544', 'Pulseira de Reposição para Smartwatch', 'Pulseira confortável e resistente, feita em silicone macio e durável. Ideal para uso diário e atividades físicas, oferece ajuste seguro no pulso e fácil instalação no relógio. Compatível com diversos modelos de smartwatch que utilizam encaixe de 18mm, 20mm ou 22mm. Design moderno, leve e fácil de limpar, perfeita para renovar o visual do seu relógio.\r\n', 15.00, 5.00, 0, NULL, 20, 'prod_20260321170855_69bec29713d45.avif', 8),
(43, '125487', 'Fone Tws Bluetooth 5.4', 'Fone TWS com cancelamento de ruído ativo, som com driver 8mm, até 22h de bateria, Bluetooth 5.4 com conexão dupla, design ergonômico e IP54.\r\nDados técnicos: Bluetooth 5.4 (A2DP/AVRCP/HFP), codec SBC, 6–7h (fones) + até 15h (case), recarga ~1,5h, 20Hz–20kHz, USB-C.', 269.03, 120.00, 0, NULL, 20, 'prod_20260320210446_69bda85e7c6dc.png', 4),
(44, '2325885', 'Monitor Gamer Curvo Concórdia Gamer ', 'Monitor LED 27\" com baixo consumo de energia, oferecendo cores vivas, ótima nitidez e resolução Full HD (1920x1080). Sua tela ampla proporciona mais conforto visual e melhor aproveitamento de espaço para multitarefas, sendo ideal para trabalho, estudos e entretenimento. Com boa fidelidade de imagem, garante uma experiência agradável ao assistir filmes, séries ou navegar no dia a dia.', 700.00, 450.00, 0, NULL, 11, 'prod_20260321172036_69bec5543dd84.png', 9),
(45, '2598566', 'Monitor Gamer Samsung Odyssey G30 24” FHD', 'Monitor gamer de 24” com resolução Full HD e taxa de atualização de 144Hz, ideal para jogos e uso diário. Possui tempo de resposta de 1ms, painel VA com alto contraste e tecnologia AMD FreeSync para imagens mais suaves e sem travamentos. Conta com design moderno sem bordas e ajuste ergonômico para maior conforto.', 1000.00, 500.00, 0, NULL, 15, 'prod_20260320210144_69bda7a89cc3a.png', 9),
(46, '44514778', 'Monitor LG UltraGear™ G4', 'Monitor gamer de 24” com tela IPS Full HD e taxa de atualização de 144Hz (overclock), oferecendo imagens fluidas e cores mais vivas. Possui tempo de resposta de 1ms (MBR), compatibilidade com NVIDIA G-SYNC e AMD FreeSync, garantindo jogabilidade sem travamentos. Conta com HDR10 e 99% sRGB para melhor qualidade de imagem, além de recursos gamer como Black Stabilizer e Crosshair para maior precisão nos jogos. Conexões HDMI, DisplayPort e saída para fone de ouvido.', 770.00, 330.00, 0, NULL, 20, 'prod_20260321171944_69bec520b349d.png', 9),
(47, '2014522', 'Monitor Profissional LG UltraFine 27\"', 'Monitor profissional de 27” com resolução 4K UHD (3840x2160), ideal para trabalho, edição e uso diário. Possui painel IPS com cores precisas e amplos ângulos de visão, além de 90% DCI-P3 para maior fidelidade em imagens. Conta com design moderno sem bordas, alto brilho e excelente qualidade visual, perfeito para quem busca desempenho e precisão em projetos criativos.', 1600.00, 1000.00, 0, NULL, 15, 'prod_20260320210620_69bda8bcc4239.png', 9),
(48, '10348787', 'Monitor Gamer LG UltraGear 27\"', 'Monitor gamer de 27” com painel IPS Full HD, taxa de atualização de 180Hz e tempo de resposta de 1ms (GtG), garantindo imagens rápidas e sem travamentos. Conta com compatibilidade com NVIDIA G-SYNC e AMD FreeSync para jogabilidade mais fluida, além de HDR10 e 99% sRGB para cores mais vivas e realistas. Possui conexões HDMI e DisplayPort, sendo ideal para quem busca desempenho e qualidade em jogos.', 1360.00, 800.00, 0, NULL, 15, 'prod_20260321171718_69bec48e93c28.png', 9),
(49, '1552245', 'Monitor Gamer Acer Nitro ', 'Monitor gamer de 23,8” com tela Full HD e painel IPS, oferecendo imagens nítidas e cores vivas. Conta com taxa de atualização de 120Hz e tempo de resposta de 1ms (VRB), garantindo mais fluidez e precisão nos jogos. Possui tecnologia Adaptive-Sync, modos de jogo e design Zero Frame, proporcionando uma experiência mais imersiva. Conexões HDMI e VGA.', 770.00, 330.00, 0, NULL, 20, 'prod_20260321164807_69bebdb7d87a3.png', 9);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produto_imagens`
--

CREATE TABLE `produto_imagens` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `imagem` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `produto_imagens`
--

INSERT INTO `produto_imagens` (`id`, `produto_id`, `imagem`) VALUES
(1, 2, 'galeria_2_1774108569_69bebf991a297.png'),
(7, 4, 'galeria_4_1774108632_69bebfd8b303d.png'),
(8, 7, 'galeria_7_1774108671_69bebfff308da.png'),
(9, 1, 'galeria_1_1774108682_69bec00aa5968.png'),
(10, 8, 'galeria_8_1774108722_69bec032004ec.png'),
(11, 8, 'galeria_8_1774108722_69bec03200b8d.png'),
(12, 31, 'galeria_31_1774108751_69bec04feab2c.png'),
(13, 33, 'galeria_33_1774108785_69bec07197d8d.png'),
(14, 30, 'galeria_30_1774108817_69bec091509b8.png'),
(15, 30, 'galeria_30_1774108817_69bec09150f3e.png'),
(16, 34, 'galeria_34_1774108836_69bec0a4c2559.png'),
(17, 29, 'galeria_29_1774108891_69bec0db887d9.png'),
(18, 29, 'galeria_29_1774108891_69bec0db88e4b.png'),
(19, 29, 'galeria_29_1774108891_69bec0db8973f.png'),
(20, 28, 'galeria_28_1774108937_69bec109e29ed.png'),
(21, 28, 'galeria_28_1774108937_69bec109e2fc1.png'),
(22, 28, 'galeria_28_1774108937_69bec109e38a0.png'),
(23, 27, 'galeria_27_1774108972_69bec12c16bb6.png'),
(24, 27, 'galeria_27_1774108972_69bec12c17419.png'),
(25, 26, 'galeria_26_1774109003_69bec14b929a0.png'),
(26, 35, 'galeria_35_1774109026_69bec162901cf.webp'),
(27, 35, 'galeria_35_1774109026_69bec16290740.webp'),
(28, 36, 'galeria_36_1774109078_69bec196e0345.webp'),
(29, 36, 'galeria_36_1774109078_69bec196e0965.webp'),
(30, 37, 'galeria_37_1774109130_69bec1ca95428.webp'),
(31, 37, 'galeria_37_1774109130_69bec1ca95933.webp'),
(32, 38, 'galeria_38_1774109156_69bec1e49bafd.png'),
(33, 38, 'galeria_38_1774109156_69bec1e49c18d.png'),
(34, 39, 'galeria_39_1774109203_69bec2134db1c.webp'),
(35, 39, 'galeria_39_1774109203_69bec2134e1b8.png'),
(36, 40, 'galeria_40_1774109260_69bec24c0646d.png'),
(38, 25, 'galeria_25_1774109406_69bec2ded4f51.png'),
(39, 25, 'galeria_25_1774109406_69bec2ded53e9.png'),
(40, 23, 'galeria_23_1774109461_69bec315733b3.png'),
(41, 17, 'galeria_17_1774109475_69bec323ba18f.png'),
(42, 17, 'galeria_17_1774109475_69bec323ba81a.png'),
(43, 21, 'galeria_21_1774109528_69bec358d02ff.png'),
(44, 21, 'galeria_21_1774109528_69bec358d08e9.png'),
(45, 20, 'galeria_20_1774109578_69bec38a24044.png'),
(46, 19, 'galeria_19_1774109652_69bec3d406b10.png'),
(47, 18, 'galeria_18_1774109683_69bec3f34a05d.png'),
(48, 18, 'galeria_18_1774109683_69bec3f34a5a0.png'),
(49, 47, 'galeria_47_1774109708_69bec40c45e82.png'),
(50, 47, 'galeria_47_1774109708_69bec40c46653.png'),
(51, 47, 'galeria_47_1774109708_69bec40c46baa.png'),
(52, 47, 'galeria_47_1774109708_69bec40c470db.png'),
(54, 48, 'galeria_48_1774109838_69bec48e94410.png'),
(55, 48, 'galeria_48_1774109838_69bec48e94896.png'),
(56, 46, 'galeria_46_1774109984_69bec520b3eb9.png'),
(57, 46, 'galeria_46_1774109984_69bec520b4382.png'),
(58, 44, 'galeria_44_1774110036_69bec5543e76a.png'),
(59, 44, 'galeria_44_1774110036_69bec5543ebe5.png'),
(60, 44, 'galeria_44_1774110036_69bec5543ef98.png'),
(61, 46, 'galeria_46_1774110059_69bec56b3e47b.webp'),
(62, 49, 'galeria_49_1774110096_69bec590e904e.png'),
(63, 49, 'galeria_49_1774110096_69bec590e97a4.png'),
(64, 49, 'galeria_49_1774110096_69bec590e9cde.png'),
(65, 49, 'galeria_49_1774110096_69bec590ea44e.png'),
(66, 49, 'galeria_49_1774110096_69bec590eab02.png'),
(69, 15, 'galeria_15_1774110171_69bec5db5ac36.png'),
(70, 15, 'galeria_15_1774110171_69bec5db5b14a.png'),
(71, 15, 'galeria_15_1774110171_69bec5db5b513.png'),
(72, 12, 'galeria_12_1774110191_69bec5efae81a.png'),
(73, 12, 'galeria_12_1774110191_69bec5efaf013.png'),
(74, 12, 'galeria_12_1774110191_69bec5efaf62e.png'),
(75, 12, 'galeria_12_1774110191_69bec5efafacd.png'),
(76, 13, 'galeria_13_1774110207_69bec5ff37508.png'),
(77, 43, 'galeria_43_1774110230_69bec61632661.png'),
(78, 43, 'galeria_43_1774110230_69bec61632d97.png'),
(79, 43, 'galeria_43_1774110230_69bec61633544.png'),
(80, 43, 'galeria_43_1774110230_69bec6163397c.png'),
(81, 14, 'galeria_14_1774110245_69bec6256b3d1.png'),
(82, 14, 'galeria_14_1774110245_69bec6256b9b4.png'),
(83, 11, 'galeria_11_1774110280_69bec64877dd3.png'),
(84, 11, 'galeria_11_1774110280_69bec6487853d.png'),
(85, 9, 'galeria_9_1774110300_69bec65c9574e.png'),
(86, 9, 'galeria_9_1774110300_69bec65c95e03.png'),
(87, 9, 'galeria_9_1774110300_69bec65c96679.png'),
(88, 9, 'galeria_9_1774110300_69bec65c96ccf.png'),
(89, 10, 'galeria_10_1774110319_69bec66f5ef6b.png'),
(90, 41, 'galeria_41_1774110484_69bec7149559f.png'),
(91, 41, 'galeria_41_1774110484_69bec71495b88.png'),
(92, 3, 'galeria_3_1774110572_69bec76c3c553.png'),
(93, 3, 'galeria_3_1774110572_69bec76c3caa4.png'),
(94, 10, 'galeria_10_1774110675_69bec7d397d8d.png'),
(95, 16, 'galeria_16_1774110770_69bec83235c6d.jpg'),
(96, 16, 'galeria_16_1774110785_69bec84102895.png'),
(97, 16, 'galeria_16_1774110785_69bec84103018.png'),
(98, 45, 'galeria_45_1774110828_69bec86c34f61.webp'),
(99, 45, 'galeria_45_1774110828_69bec86c356bf.webp'),
(100, 45, 'galeria_45_1774110828_69bec86c35f2f.webp'),
(101, 45, 'galeria_45_1774110828_69bec86c3648e.png'),
(102, 45, 'galeria_45_1774110828_69bec86c36d90.webp'),
(103, 24, 'galeria_24_1774110897_69bec8b1e10e0.png'),
(104, 32, 'galeria_32_1774110964_69bec8f48e117.png'),
(105, 42, 'galeria_42_1774111064_69bec95813ff9.png'),
(106, 42, 'galeria_42_1774111064_69bec958145d5.png'),
(107, 22, 'galeria_22_1774111091_69bec97369088.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `sistema_logs`
--

CREATE TABLE `sistema_logs` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(255) NOT NULL,
  `data_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `sistema_logs`
--

INSERT INTO `sistema_logs` (`id`, `usuario_id`, `acao`, `data_hora`) VALUES
(1, 2, 'Login: Usuário andelson entrou no sistema', '2025-12-26 19:35:21'),
(2, 2, 'Login: Usuário andelson entrou no sistema', '2025-12-26 23:55:26'),
(3, 2, 'Login: Usuário andelson entrou no sistema', '2025-12-27 12:31:20'),
(4, 1, 'Login: Usuário zezim entrou no sistema', '2026-01-17 17:06:34'),
(5, 1, 'Login: Usuário zezim entrou no sistema', '2026-01-17 17:09:04'),
(6, 4, 'Login: Usuário Sarah Stephanie entrou.', '2026-01-24 15:00:15'),
(7, 4, 'Login: Usuário Sarah Stephanie entrou.', '2026-01-24 15:01:03'),
(8, 4, 'Login: Usuário Sarah Stephanie entrou.', '2026-01-24 15:03:53'),
(9, 5, 'Login: Usuário Andelson nascimento entrou.', '2026-01-24 15:12:20'),
(10, 5, 'Login: Usuário Andelson nascimento entrou.', '2026-01-24 15:20:49'),
(11, 5, 'Login: Usuário Andelson nascimento entrou.', '2026-01-24 15:21:56'),
(12, 6, 'Login: Usuário Andelson nascimento entrou.', '2026-01-24 15:23:07'),
(13, 2, 'Login: Usuário Andelson entrou.', '2026-01-26 23:55:45'),
(14, 2, 'Login: Usuário Andelson entrou.', '2026-01-27 09:58:02'),
(15, 7, 'Login: Usuário Andelson Nascimento entrou.', '2026-03-02 09:47:50'),
(16, 7, 'Login: Usuário Andelson Nascimento entrou.', '2026-03-02 09:54:08'),
(17, 7, 'Login: Usuário Andelson Nascimento entrou.', '2026-03-02 10:19:59'),
(18, 7, 'Login: Usuário Andelson Nascimento entrou.', '2026-03-02 10:20:52'),
(19, 7, 'Login: Usuário Andelson Nascimento entrou.', '2026-03-02 10:24:12'),
(20, 1, 'Login: Usuário Zezinho entrou.', '2026-03-02 10:25:14'),
(21, 2, 'Login: Usuário Andelson entrou.', '2026-03-09 14:40:20'),
(22, 2, 'Login: Usuário Andelson entrou.', '2026-03-09 14:43:42'),
(23, 2, 'Login: Usuário Andelson entrou.', '2026-03-09 14:57:38'),
(24, 2, 'Login: Usuário Andelson entrou.', '2026-03-09 14:59:31'),
(25, 2, 'Login: Usuário Andelson entrou.', '2026-03-09 15:02:06'),
(26, 2, 'Login: Usuário Andelson entrou.', '2026-03-09 15:04:21'),
(27, 2, 'Login: Usuário Andelson entrou.', '2026-03-17 15:09:16'),
(28, 2, 'Login: Usuário Andelson entrou.', '2026-03-17 15:10:50'),
(29, 2, 'Login: Usuário Andelson entrou.', '2026-03-17 15:12:29'),
(30, 2, 'Login: Usuário Andelson entrou.', '2026-03-18 14:55:25'),
(31, 2, 'Login: Usuário Andelson entrou.', '2026-03-19 14:10:43'),
(32, 2, 'Login: Usuário Andelson entrou.', '2026-03-19 15:02:53'),
(33, 2, 'Login: Usuário Andelson entrou.', '2026-03-19 16:36:34'),
(34, 2, 'Login: Usuário Andelson entrou.', '2026-03-19 16:41:52'),
(35, 2, 'Login: Usuário Andelson entrou.', '2026-03-19 16:42:51'),
(36, 2, 'Login: Usuário Andelson entrou.', '2026-03-20 13:59:08'),
(37, 2, 'Login: Usuário Andelson entrou.', '2026-03-20 15:20:14'),
(38, 2, 'Login: Usuário Andelson entrou.', '2026-03-20 15:24:12'),
(39, 2, 'Login: Usuário Andelson entrou.', '2026-03-20 16:31:34'),
(40, 2, 'Login: Usuário Andelson entrou.', '2026-03-20 16:49:01'),
(41, 2, 'Login: Usuário Andelson entrou.', '2026-03-21 12:37:14');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `nivel` enum('usuario','admin','master') NOT NULL DEFAULT 'usuario',
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `google_id`, `usuario`, `senha`, `nivel`, `foto`) VALUES
(1, 'Zezinho', NULL, NULL, 'zezim', '$2y$10$YdzMee5sCI1Rvfa27JoxreidzKCWdfwiIABn5bFI1XR/jwiykiXtm', 'master', NULL),
(2, 'Andelson', NULL, NULL, 'andelson', '$2y$10$YdzMee5sCI1Rvfa27JoxreidzKCWdfwiIABn5bFI1XR/jwiykiXtm', 'master', NULL),
(3, 'Sarah', NULL, NULL, 'sarah', '$2y$10$YdzMee5sCI1Rvfa27JoxreidzKCWdfwiIABn5bFI1XR/jwiykiXtm', 'usuario', NULL),
(4, 'Sarah Stephanie', 'stephaniemoura285@gmail.com', '118063862504631323329', 'stephaniemoura285', '', 'usuario', 'https://lh3.googleusercontent.com/a/ACg8ocKM2vBoB7D7TgembzR4xIlfZye6MHuIJlSmovqmhQNfnD4myk9n=s96-c'),
(5, 'Andelson nascimento', '', '', '', '', '', ''),
(6, 'Andelson nascimento', 'geovanesilva301@gmail.com', '112725934260905830548', 'geovanesilva301', '', '', 'https://lh3.googleusercontent.com/a/ACg8ocLj7pL49wvcEq51avYMRBIc-PFcnFqgUdzaF8cqDkhnTJOXrk_xWA=s96-c'),
(7, 'Andelson Nascimento', 'andelsonascimento@gmail.com', '109035660575826182106', 'andelsonascimento', '', '', 'https://lh3.googleusercontent.com/a/ACg8ocLzPhlA0uMuEugcpNd18R3XujL0KD8i-USXeOC0gcAZGSMs6lAr=s96-c');

-- --------------------------------------------------------

--
-- Estrutura da tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `data_venda` datetime DEFAULT current_timestamp(),
  `cliente_id` int(11) DEFAULT NULL,
  `forma_pagamento` varchar(30) DEFAULT 'Dinheiro',
  `valor_pago` decimal(10,2) DEFAULT 0.00,
  `status_pagamento` varchar(20) DEFAULT 'pago',
  `observacoes` varchar(255) DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `tipo_entrega` varchar(20) DEFAULT 'retirada',
  `endereco_entrega` text DEFAULT NULL,
  `data_entrega` datetime DEFAULT NULL,
  `status_entrega` varchar(20) DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `vendas`
--

INSERT INTO `vendas` (`id`, `usuario_id`, `total`, `data_venda`, `cliente_id`, `forma_pagamento`, `valor_pago`, `status_pagamento`, `observacoes`, `data_pagamento`, `tipo_entrega`, `endereco_entrega`, `data_entrega`, `status_entrega`) VALUES
(11, 2, 36.90, '2025-12-27 12:31:43', 1, 'Dinheiro', 36.90, 'pago', NULL, NULL, 'retirada', NULL, NULL, 'pendente'),
(14, NULL, 34.70, '2026-01-24 23:16:58', 2, 'Dinheiro', 34.70, 'pago', 'Retirada na Loja', NULL, 'retirada', NULL, NULL, 'pendente'),
(15, NULL, 49.80, '2026-01-24 23:22:02', 2, 'Dinheiro', 0.00, 'pendente', 'Retirada na Loja', NULL, 'retirada', NULL, NULL, 'pendente'),
(16, NULL, 81.30, '2026-01-24 23:31:55', 2, 'Dinheiro', 0.00, 'pendente', 'Retirada na Loja', NULL, 'retirada', NULL, NULL, 'pendente'),
(17, 2, 27.90, '2026-01-27 10:18:37', 1, 'Pix', 27.90, 'pago', NULL, NULL, 'retirada', NULL, NULL, 'pendente'),
(20, 2, 47.40, '2026-01-27 10:29:32', 1, 'Cartão de Débito', 47.40, 'pago', 'Entrega: Avenida Ferroviária, 81 - Vila dos Pescadores', '2026-01-27', 'entrega', 'Avenida Ferroviária, 81 - Vila dos Pescadores', NULL, 'entregue'),
(21, 2, 24.90, '2026-01-27 10:34:05', 1, 'Cartão de Crédito', 24.90, 'pago', 'Entrega: Avenida Ferroviária, 81 - Vila dos Pescadores', '2026-01-27', 'entrega', 'Avenida Ferroviária, 81 - Vila dos Pescadores', NULL, 'entregue'),
(22, 2, 3.00, '2026-01-27 10:35:35', 1, 'Dinheiro', 3.00, 'pago', 'Entrega: Avenida Ferroviária, 81 - Vila dos Pescadores', '2026-01-27', 'entrega', 'Avenida Ferroviária, 81 - Vila dos Pescadores', NULL, 'pendente'),
(23, 2, 36.90, '2026-01-27 10:37:32', 1, 'Cartão de Débito', 36.90, 'pago', 'Entrega: Avenida Ferroviária, 81 - Vila dos Pescadores', '2026-01-27', 'entrega', 'Avenida Ferroviária, 81 - Vila dos Pescadores', '2026-01-27 12:00:00', 'pendente'),
(24, NULL, 157.20, '2026-01-28 05:26:42', 2, 'Dinheiro', 0.00, 'pendente', 'Endereço principal do cadastro', NULL, 'retirada', NULL, NULL, 'pendente'),
(25, NULL, 15.95, '2026-01-28 05:42:52', 2, 'Dinheiro', 0.00, 'pendente', 'Endereço principal do cadastro', NULL, 'retirada', NULL, NULL, 'pendente'),
(30, NULL, 49.85, '2026-01-28 05:58:25', 2, 'Dinheiro', 49.85, 'pago', 'Endereço principal do cadastro', NULL, 'retirada', NULL, NULL, 'pendente'),
(31, NULL, 36.90, '2026-01-28 06:02:28', 2, 'Dinheiro', 0.00, 'pendente', 'Endereço principal do cadastro', NULL, 'retirada', NULL, NULL, 'pendente'),
(32, NULL, 27.90, '2026-01-28 06:04:36', 2, 'Dinheiro', 27.90, 'pago', 'Endereço principal do cadastro', NULL, 'retirada', NULL, NULL, 'pendente'),
(34, NULL, 57.85, '2026-01-28 06:07:25', 2, 'pix', 0.00, 'pendente', 'Venda Online (MP ID: 1344264265)', NULL, 'entrega', 'Avenida Ferroviária, 81 - Vila dos Pescadores', '2026-01-30 12:00:00', 'pendente'),
(35, NULL, 1.60, '2026-03-02 09:44:55', 4, 'pix', 0.00, 'pendente', 'Venda Online (MP ID: 1326450484)', NULL, 'retirada', 'Retirada na Loja', NULL, 'pendente');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices para tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Índices para tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venda_id` (`venda_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices para tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venda_id` (`venda_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices para tabela `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produto_imagens` (`produto_id`);

--
-- Índices para tabela `sistema_logs`
--
ALTER TABLE `sistema_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Índices para tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de tabela `produto_imagens`
--
ALTER TABLE `produto_imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de tabela `sistema_logs`
--
ALTER TABLE `sistema_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD CONSTRAINT `itens_venda_ibfk_1` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`),
  ADD CONSTRAINT `itens_venda_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Limitadores para a tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`),
  ADD CONSTRAINT `pagamentos_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Limitadores para a tabela `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Limitadores para a tabela `produto_imagens`
--
ALTER TABLE `produto_imagens`
  ADD CONSTRAINT `fk_produto_imagens` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `sistema_logs`
--
ALTER TABLE `sistema_logs`
  ADD CONSTRAINT `sistema_logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `vendas_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
