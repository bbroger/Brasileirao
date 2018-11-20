-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 20-Nov-2018 às 12:38
-- Versão do servidor: 10.1.33-MariaDB
-- PHP Version: 7.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brasileirao`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cac_cadastrar_copas`
--

CREATE TABLE `cac_cadastrar_copas` (
  `cac_id_cadastro_copa` int(11) NOT NULL,
  `cac_id_copa` int(2) NOT NULL,
  `cac_id_liga` int(11) DEFAULT NULL,
  `cac_rodada` int(2) NOT NULL,
  `cac_posicao` int(2) NOT NULL,
  `cac_oitavas` int(11) NOT NULL,
  `cac_quartas` int(11) DEFAULT NULL,
  `cac_semi` int(11) DEFAULT NULL,
  `cac_final` int(11) DEFAULT NULL,
  `cac_campeao` int(11) DEFAULT NULL,
  `cac_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cad_cadastrar_rodadas`
--

CREATE TABLE `cad_cadastrar_rodadas` (
  `cad_id_cadastro` int(11) NOT NULL,
  `cad_rodada` int(2) NOT NULL,
  `cad_partida` int(2) NOT NULL,
  `cad_time_mandante` varchar(50) NOT NULL,
  `cad_time_visitante` varchar(50) NOT NULL,
  `cad_time_mandante_var` varchar(20) NOT NULL,
  `cad_time_visitante_var` varchar(20) NOT NULL,
  `cad_time_mandante_gol` int(2) DEFAULT NULL,
  `cad_time_visitante_gol` int(2) DEFAULT NULL,
  `cad_local` varchar(20) NOT NULL DEFAULT 'Sem informação',
  `cad_data` datetime NOT NULL,
  `cad_adiou` varchar(3) NOT NULL,
  `cad_passou` varchar(10) NOT NULL DEFAULT 'nao',
  `cad_user_id` int(11) NOT NULL,
  `cad_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cdd_confronto_desafios_dupla`
--

CREATE TABLE `cdd_confronto_desafios_dupla` (
  `cdd_id_confronto` int(11) NOT NULL,
  `cdd_rodada` int(2) NOT NULL,
  `cdd_id_dupla_um` int(11) NOT NULL,
  `cdd_id_dupla_dois` int(11) NOT NULL,
  `cdd_dupla_vencedor` int(11) NOT NULL,
  `cdd_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ded_desafios_dupla`
--

CREATE TABLE `ded_desafios_dupla` (
  `ded_id_dulpa` int(11) NOT NULL,
  `ded_rodada` int(2) NOT NULL,
  `ded_id_dupla_um` int(11) NOT NULL,
  `ded_id_dupla_dois` int(11) NOT NULL,
  `ded_pontos_um` int(2) DEFAULT NULL,
  `ded_lucro_um` float(5,2) DEFAULT NULL,
  `ded_pontos_dois` int(2) DEFAULT NULL,
  `ded_lucro_dois` float(5,2) DEFAULT NULL,
  `ded_status` varchar(10) NOT NULL DEFAULT 'pendente',
  `ded_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `dei_desafios_individual`
--

CREATE TABLE `dei_desafios_individual` (
  `dei_id_desafio` int(11) NOT NULL,
  `dei_rodada` int(2) NOT NULL,
  `dei_id_user_desafiador` int(11) NOT NULL,
  `dei_id_user_desafiado` int(11) NOT NULL,
  `dei_pontos_desafiador` int(2) DEFAULT NULL,
  `dei_lucro_desafiador` float(5,2) DEFAULT NULL,
  `dei_pontos_desafiado` int(2) DEFAULT NULL,
  `dei_lucro_desafiado` float(5,2) DEFAULT NULL,
  `dei_vencedor` int(11) DEFAULT NULL,
  `dei_status` varchar(10) NOT NULL DEFAULT 'pendente',
  `dei_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `lig_ligas`
--

CREATE TABLE `lig_ligas` (
  `lig_id_liga` int(11) NOT NULL,
  `lig_nome` varchar(20) NOT NULL,
  `lig_descricao` varchar(100) NOT NULL,
  `lig_img` varchar(255) NOT NULL DEFAULT 'default.png',
  `lig_status` varchar(10) NOT NULL DEFAULT 'ativo',
  `lig_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mel_membros_ligas`
--

CREATE TABLE `mel_membros_ligas` (
  `mel_id_inscricao` int(11) NOT NULL,
  `mel_id_liga` int(11) NOT NULL,
  `mel_id_user` int(11) NOT NULL,
  `mel_status` varchar(20) NOT NULL DEFAULT 'ativo',
  `mel_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pap_palpites`
--

CREATE TABLE `pap_palpites` (
  `pap_id_palpite` int(11) NOT NULL,
  `pap_user_id` int(11) NOT NULL,
  `pap_rodada` int(2) NOT NULL,
  `pap_partida` int(2) NOT NULL,
  `pap_gol_mandante` int(2) DEFAULT NULL,
  `pap_gol_visitante` int(2) DEFAULT NULL,
  `pap_aposta` int(3) DEFAULT NULL,
  `pap_cc` int(2) NOT NULL DEFAULT '0',
  `pap_ct` int(2) NOT NULL DEFAULT '0',
  `pap_cf` int(2) NOT NULL DEFAULT '0',
  `pap_pontos` int(4) NOT NULL DEFAULT '0',
  `pap_lucro` float(5,2) NOT NULL DEFAULT '0.00',
  `pap_saldo` float(5,2) NOT NULL DEFAULT '0.00',
  `pap_adiou` varchar(3) NOT NULL DEFAULT 'nao',
  `pap_palpitou` varchar(3) NOT NULL,
  `pap_valida` varchar(3) NOT NULL DEFAULT 'sim',
  `pap_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tim_times`
--

CREATE TABLE `tim_times` (
  `tim_id` int(11) NOT NULL,
  `tim_nome` varchar(20) NOT NULL,
  `tim_nome_completo` varchar(50) NOT NULL,
  `tim_var` varchar(20) NOT NULL,
  `tim_abreviado` varchar(3) NOT NULL,
  `tim_primeira_cor` varchar(10) NOT NULL,
  `tim_segunda_cor` varchar(10) NOT NULL,
  `tim_first_color` varchar(10) NOT NULL,
  `tim_second_color` varchar(10) NOT NULL,
  `tim_serie` varchar(1) NOT NULL DEFAULT 'a',
  `tim_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `tim_times`
--

INSERT INTO `tim_times` (`tim_id`, `tim_nome`, `tim_nome_completo`, `tim_var`, `tim_abreviado`, `tim_primeira_cor`, `tim_segunda_cor`, `tim_first_color`, `tim_second_color`, `tim_serie`, `tim_created`) VALUES
(1, 'América-MG', 'América Futebol Clube', 'america-mg', 'AME', 'verde', 'branco', 'green', 'white', 'a', '2018-08-22 14:42:18'),
(2, 'Atlético-MG', 'Clube Atlético Mineiro', 'atletico-mg', 'CAM', 'preto', 'branco', 'black', 'white', 'a', '2018-08-22 14:42:18'),
(3, 'Atlético-PR', 'Clube Atlético Paranaense', 'atletico-pr', 'CAP', 'vermelho', 'preto', 'red', 'black', 'a', '2018-08-22 14:42:18'),
(4, 'Bahia', 'Esporte Clube Bahia', 'bahia', 'BAH', 'azul', 'branco', 'blue', 'white', 'a', '2018-08-22 14:45:28'),
(5, 'Botafogo', 'Botafogo de Futebol e Regatas', 'botafogo', 'BOT', 'preto', 'branco', 'black', 'white', 'a', '2018-08-22 14:50:21'),
(6, 'Ceará', 'Ceará Sporting Club', 'ceara', 'CEA', 'preto', 'branco', 'black', 'white', 'a', '2018-08-22 14:50:21'),
(7, 'Chapecoense', 'Associação Chapecoense de Futebol', 'chapecoense', 'CHA', 'verde', 'branco', 'green', 'white', 'a', '2018-08-22 14:50:21'),
(8, 'Corinthians', 'Sport Club Corinthians Paulista', 'corinthians', 'COR', 'preto', 'branco', 'black', 'white', 'a', '2018-08-22 14:50:21'),
(9, 'Cruzeiro', 'Cruzeiro Esporte Clube', 'cruzeiro', 'CRU', 'azul', 'branco', 'blue', 'white', 'a', '2018-08-22 14:50:21'),
(10, 'Flamengo', 'Clube de Regatas do Flamengo', 'flamengo', 'FLA', 'vermelho', 'preto', 'red', 'black', 'a', '2018-08-22 14:57:15'),
(11, 'Fluminense', 'Fluminense Football Club', 'fluminense', 'FLU', 'branco', 'verde', 'white', 'green', 'a', '2018-08-22 15:15:06'),
(12, 'Grêmio', 'Grêmio Foot-Ball Porto Alegrense', 'gremio', 'GRE', 'azul', 'branco', 'blue', 'white', 'a', '2018-08-22 15:15:06'),
(13, 'Internacional', 'Sport Club Internacional', 'internacional', 'INT', 'vermelho', 'branco', 'red', 'white', 'a', '2018-08-22 15:15:06'),
(14, 'Palmeiras', 'Sociedade Esportiva Palmeiras', 'palmeiras', 'PAL', 'verde', 'branco', 'green', 'white', 'a', '2018-08-22 15:15:06'),
(15, 'Paraná', 'Paraná Clube', 'parana', 'PAR', 'azul', 'branco', 'blue', 'white', 'a', '2018-08-22 15:15:06'),
(16, 'Santos', 'Santos Futebol Clube', 'santos', 'SAN', 'preto', 'branco', 'black', 'white', 'a', '2018-08-22 15:15:06'),
(17, 'São Paulo', 'São Paulo Futebol Clube', 'sao-paulo', 'SAO', 'branco', 'vermelho', 'white', 'red', 'a', '2018-08-22 15:15:06'),
(18, 'Sport', 'Sport Club do Recife', 'sport', 'SPO', 'vermelho', 'preto', 'red', 'black', 'a', '2018-08-22 15:15:06'),
(19, 'Vasco', 'Club de Regatas Vasco da Gama', 'vasco', 'VAS', 'preto', 'branco', 'black', 'white', 'a', '2018-08-22 15:15:06'),
(20, 'Vitória', 'Esporte Clube Vitória', 'vitoria', 'VIT', 'vermelho', 'branco', 'red', 'white', 'a', '2018-08-22 15:15:06');

-- --------------------------------------------------------

--
-- Estrutura da tabela `use_users`
--

CREATE TABLE `use_users` (
  `use_id_user` int(11) NOT NULL,
  `use_nickname` varchar(15) NOT NULL,
  `use_name` varchar(50) NOT NULL,
  `use_email` varchar(50) NOT NULL,
  `use_password` varchar(50) NOT NULL,
  `use_img_perfil` varchar(100) NOT NULL DEFAULT 'default.png',
  `use_type` varchar(10) NOT NULL DEFAULT 'normal',
  `use_mangos` float(5,2) NOT NULL DEFAULT '100.00',
  `use_status` varchar(10) NOT NULL DEFAULT 'ativo',
  `use_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `use_users`
--

INSERT INTO `use_users` (`use_id_user`, `use_nickname`, `use_name`, `use_email`, `use_password`, `use_img_perfil`, `use_type`, `use_mangos`, `use_status`, `use_created`) VALUES
(1, 'Visitante', 'Visitante', 'visitante@visitante.com', 'visitante', 'perfil2.jpg', 'normal', 100.00, 'ativo', '2018-09-22 21:04:14'),
(2, 'Goodnato', 'Renato Nascimento', 'renato.95x@gmail.com', 'goodlegends3d', 'perfil.jpg', 'normal', 100.00, 'ativo', '2018-09-22 21:04:14'),
(3, 'Jerisbaldo', 'Jerisbaldo', 'jerisbaldo@gmail.com', 'goodlegends3d', 'default.png', 'normal', 100.00, 'ativo', '2018-09-23 15:44:17'),
(4, 'Beribei', 'Berivaldo Beribei', 'beribei@gmail.com', 'goodlegends3d', 'default.png', 'normal', 100.00, 'ativo', '2018-09-25 01:59:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cac_cadastrar_copas`
--
ALTER TABLE `cac_cadastrar_copas`
  ADD PRIMARY KEY (`cac_id_cadastro_copa`);

--
-- Indexes for table `cad_cadastrar_rodadas`
--
ALTER TABLE `cad_cadastrar_rodadas`
  ADD PRIMARY KEY (`cad_id_cadastro`);

--
-- Indexes for table `cdd_confronto_desafios_dupla`
--
ALTER TABLE `cdd_confronto_desafios_dupla`
  ADD PRIMARY KEY (`cdd_id_confronto`);

--
-- Indexes for table `ded_desafios_dupla`
--
ALTER TABLE `ded_desafios_dupla`
  ADD PRIMARY KEY (`ded_id_dulpa`);

--
-- Indexes for table `dei_desafios_individual`
--
ALTER TABLE `dei_desafios_individual`
  ADD PRIMARY KEY (`dei_id_desafio`);

--
-- Indexes for table `lig_ligas`
--
ALTER TABLE `lig_ligas`
  ADD PRIMARY KEY (`lig_id_liga`);

--
-- Indexes for table `mel_membros_ligas`
--
ALTER TABLE `mel_membros_ligas`
  ADD PRIMARY KEY (`mel_id_inscricao`);

--
-- Indexes for table `pap_palpites`
--
ALTER TABLE `pap_palpites`
  ADD PRIMARY KEY (`pap_id_palpite`);

--
-- Indexes for table `tim_times`
--
ALTER TABLE `tim_times`
  ADD PRIMARY KEY (`tim_id`);

--
-- Indexes for table `use_users`
--
ALTER TABLE `use_users`
  ADD PRIMARY KEY (`use_id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cac_cadastrar_copas`
--
ALTER TABLE `cac_cadastrar_copas`
  MODIFY `cac_id_cadastro_copa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cad_cadastrar_rodadas`
--
ALTER TABLE `cad_cadastrar_rodadas`
  MODIFY `cad_id_cadastro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cdd_confronto_desafios_dupla`
--
ALTER TABLE `cdd_confronto_desafios_dupla`
  MODIFY `cdd_id_confronto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ded_desafios_dupla`
--
ALTER TABLE `ded_desafios_dupla`
  MODIFY `ded_id_dulpa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dei_desafios_individual`
--
ALTER TABLE `dei_desafios_individual`
  MODIFY `dei_id_desafio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lig_ligas`
--
ALTER TABLE `lig_ligas`
  MODIFY `lig_id_liga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mel_membros_ligas`
--
ALTER TABLE `mel_membros_ligas`
  MODIFY `mel_id_inscricao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pap_palpites`
--
ALTER TABLE `pap_palpites`
  MODIFY `pap_id_palpite` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tim_times`
--
ALTER TABLE `tim_times`
  MODIFY `tim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `use_users`
--
ALTER TABLE `use_users`
  MODIFY `use_id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
