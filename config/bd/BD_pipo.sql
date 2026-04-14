-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30-Set-2025 às 18:26
-- Versão do servidor: 10.4.27-MariaDB
-- versão do PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pipo`
--
CREATE DATABASE IF NOT EXISTS `pipo` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pipo`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(4) NOT NULL,
  `codigo_admin` varchar(10) NOT NULL,
  `nome_admin` varchar(40) NOT NULL,
  `senha_admin` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `admin`
--

INSERT INTO `admin` (`id_admin`, `codigo_admin`, `nome_admin`, `senha_admin`) VALUES
(1, '2025307960', 'Mikael Felipe Silva Braga', '$2y$10$2535S');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cardapio`
--

CREATE TABLE `cardapio` (
  `id_cardapio` int(10) NOT NULL,
  `nome_cardapio` varchar(30) NOT NULL,
  `prato_A_cardapio` int(10) DEFAULT NULL,
  `prato_B_cardapio` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fila`
--

CREATE TABLE `fila` (
  `id_fila` int(10) NOT NULL,
  `data_fila` date NOT NULL,
  `cardapio_fila` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `grupo`
--

CREATE TABLE `grupo` (
  `id_grupo` int(4) NOT NULL,
  `codigo_grupo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `participante_grupo`
--

CREATE TABLE `participante_grupo` (
  `id_participante_grupo` int(4) NOT NULL,
  `grupo_participado` int(4) NOT NULL,
  `usuario_participado` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `prato`
--

CREATE TABLE `prato` (
  `id_prato` int(11) NOT NULL,
  `nome_prato` varchar(255) NOT NULL,
  `descricao_prato` text DEFAULT NULL,
  `quantidade_disponivel_prato` int(11) NOT NULL,
  `data_prato` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `reserva`
--

CREATE TABLE `reserva` (
  `id_reserva` int(10) NOT NULL,
  `data_reserva` date NOT NULL,
  `tipo_reserva` enum('normal','prioridade','reserva') NOT NULL,
  `status_reserva` enum('ativa','cancelada','inválida','concluída') NOT NULL,
  `prato_prioritario_reserva` int(10) DEFAULT NULL,
  `id_grupo_reserva` int(4) DEFAULT NULL,
  `id_fila_reserva` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_user` int(4) NOT NULL,
  `nome_user` varchar(40) NOT NULL,
  `email_user` varchar(40) NOT NULL,
  `senha_user` varchar(12) NOT NULL,
  `matricula_user` varchar(19) NOT NULL,
  `categoria_user` varchar(20) NOT NULL,
  `grupo_user` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `codigo_admin` (`codigo_admin`);

--
-- Índices para tabela `cardapio`
--
ALTER TABLE `cardapio`
  ADD PRIMARY KEY (`id_cardapio`),
  ADD KEY `prato_A_cardapio` (`prato_A_cardapio`),
  ADD KEY `prato_B_cardapio` (`prato_B_cardapio`);

--
-- Índices para tabela `fila`
--
ALTER TABLE `fila`
  ADD PRIMARY KEY (`id_fila`),
  ADD KEY `cardapio_fila` (`cardapio_fila`);

--
-- Índices para tabela `grupo`
--
ALTER TABLE `grupo`
  ADD PRIMARY KEY (`id_grupo`),
  ADD UNIQUE KEY `codigo_grupo` (`codigo_grupo`);

--
-- Índices para tabela `participante_grupo`
--
ALTER TABLE `participante_grupo`
  ADD PRIMARY KEY (`id_participante_grupo`),
  ADD KEY `grupo_participado` (`grupo_participado`),
  ADD KEY `usuario_participado` (`usuario_participado`);

--
-- Índices para tabela `prato`
--
ALTER TABLE `prato`
  ADD PRIMARY KEY (`id_prato`);

--
-- Índices para tabela `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `prato_prioritario_reserva` (`prato_prioritario_reserva`),
  ADD KEY `id_grupo_reserva` (`id_grupo_reserva`),
  ADD KEY `id_fila_reserva` (`id_fila_reserva`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `grupo_user` (`grupo_user`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `cardapio`
--
ALTER TABLE `cardapio`
  MODIFY `id_cardapio` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fila`
--
ALTER TABLE `fila`
  MODIFY `id_fila` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `grupo`
--
ALTER TABLE `grupo`
  MODIFY `id_grupo` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `participante_grupo`
--
ALTER TABLE `participante_grupo`
  MODIFY `id_participante_grupo` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `prato`
--
ALTER TABLE `prato`
  MODIFY `id_prato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id_reserva` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_user` int(4) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `cardapio`
--
ALTER TABLE `cardapio`
  ADD CONSTRAINT `cardapio_ibfk_1` FOREIGN KEY (`prato_A_cardapio`) REFERENCES `prato` (`id_prato`),
  ADD CONSTRAINT `cardapio_ibfk_2` FOREIGN KEY (`prato_B_cardapio`) REFERENCES `prato` (`id_prato`);

--
-- Limitadores para a tabela `fila`
--
ALTER TABLE `fila`
  ADD CONSTRAINT `fila_ibfk_1` FOREIGN KEY (`cardapio_fila`) REFERENCES `cardapio` (`id_cardapio`);

--
-- Limitadores para a tabela `participante_grupo`
--
ALTER TABLE `participante_grupo`
  ADD CONSTRAINT `participante_grupo_ibfk_1` FOREIGN KEY (`grupo_participado`) REFERENCES `grupo` (`id_grupo`),
  ADD CONSTRAINT `participante_grupo_ibfk_2` FOREIGN KEY (`usuario_participado`) REFERENCES `usuario` (`id_user`);

--
-- Limitadores para a tabela `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`prato_prioritario_reserva`) REFERENCES `prato` (`id_prato`),
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`id_grupo_reserva`) REFERENCES `grupo` (`id_grupo`),
  ADD CONSTRAINT `reserva_ibfk_3` FOREIGN KEY (`id_fila_reserva`) REFERENCES `fila` (`id_fila`);

--
-- Limitadores para a tabela `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`grupo_user`) REFERENCES `grupo` (`id_grupo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
