-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27/10/2026 às 02:19
-- Versão do servidor: 10.11.16-MariaDB
-- Versão do PHP: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gerenciador_logistico`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `cidade` varchar(100) NOT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `rua` varchar(150) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `enderecos`
--

INSERT INTO `enderecos` (`id`, `nome`, `cep`, `estado`, `cidade`, `bairro`, `rua`, `numero`, `complemento`, `criado_em`) VALUES
(1, 'Depósito principal', '85801-000', 'PR', 'Cascavel', 'Centro', 'Rua Paraná', '100', 'Galpão 1', '2026-10-26 23:42:59'),
(2, 'Depósito teste', '85803170', 'PR', 'Cascavel', 'Santa Felicidade', 'Andréa Galafassi', '98', 'Galpão 3 atualizado', '2026-10-26 23:55:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fretes`
--

CREATE TABLE `fretes` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `origem_id` int(11) NOT NULL,
  `destino_id` int(11) NOT NULL,
  `transportador_id` int(11) NOT NULL,
  `data_envio` date DEFAULT NULL,
  `data_entrega` date DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pendente','Em trânsito','Entregue','Cancelado') NOT NULL DEFAULT 'Pendente',
  `observacoes` text DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fretes`
--

INSERT INTO `fretes` (`id`, `produto_id`, `origem_id`, `destino_id`, `transportador_id`, `data_envio`, `data_entrega`, `valor`, `status`, `observacoes`, `criado_em`) VALUES
(1, 1, 1, 2, 1, '2026-10-26', '2026-10-27', 150.00, 'Pendente', 'Frete de teste do sistema', '2026-10-26 23:56:24'),
(2, 1, 1, 2, 1, '2026-10-26', '2026-10-27', 200.00, 'Pendente', NULL, '2026-10-27 00:17:59');

-- --------------------------------------------------------

--
-- Estrutura para tabela `frete_produtos`
--

CREATE TABLE `frete_produtos` (
  `id` int(11) NOT NULL,
  `frete_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `frete_produtos`
--

INSERT INTO `frete_produtos` (`id`, `frete_id`, `produto_id`, `quantidade`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 2),
(3, 2, 2, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `comprimento` decimal(10,2) DEFAULT NULL,
  `largura` decimal(10,2) DEFAULT NULL,
  `altura` decimal(10,2) DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `peso`, `comprimento`, `largura`, `altura`, `criado_em`) VALUES
(1, 'Caixa de papelão', 'Embalagem para transporte de mercadorias atualizado', 2.00, 40.00, 30.00, 20.00, '2026-10-26 23:39:39'),
(2, 'Produto Teste 2', 'Para teste', 1.00, 2.00, 3.00, 4.00, '2026-10-27 00:17:04');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transportadores`
--

CREATE TABLE `transportadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `transportadores`
--

INSERT INTO `transportadores` (`id`, `nome`, `cnpj`, `telefone`, `email`, `criado_em`) VALUES
(1, 'Transportadora Exemplo', NULL, '(45) 99999-0000 att', 'contato@exemplo.com.br', '2026-10-26 23:50:21');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fretes`
--
ALTER TABLE `fretes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`),
  ADD KEY `origem_id` (`origem_id`),
  ADD KEY `destino_id` (`destino_id`),
  ADD KEY `transportador_id` (`transportador_id`);

--
-- Índices de tabela `frete_produtos`
--
ALTER TABLE `frete_produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `frete_id` (`frete_id`,`produto_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `transportadores`
--
ALTER TABLE `transportadores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `fretes`
--
ALTER TABLE `fretes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `frete_produtos`
--
ALTER TABLE `frete_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `transportadores`
--
ALTER TABLE `transportadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `fretes`
--
ALTER TABLE `fretes`
  ADD CONSTRAINT `fretes_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`),
  ADD CONSTRAINT `fretes_ibfk_2` FOREIGN KEY (`origem_id`) REFERENCES `enderecos` (`id`),
  ADD CONSTRAINT `fretes_ibfk_3` FOREIGN KEY (`destino_id`) REFERENCES `enderecos` (`id`),
  ADD CONSTRAINT `fretes_ibfk_4` FOREIGN KEY (`transportador_id`) REFERENCES `transportadores` (`id`);

--
-- Restrições para tabelas `frete_produtos`
--
ALTER TABLE `frete_produtos`
  ADD CONSTRAINT `frete_produtos_ibfk_1` FOREIGN KEY (`frete_id`) REFERENCES `fretes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `frete_produtos_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
