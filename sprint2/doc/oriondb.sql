-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/03/2026 às 20:48
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `oriondb`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `action_type` enum('create','update','delete') DEFAULT NULL,
  `entity_type` enum('user','movie') DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `entity_name` varchar(255) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `admin_username` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `action_type`, `entity_type`, `entity_id`, `entity_name`, `admin_id`, `admin_username`, `created_at`) VALUES
(34, 'delete', 'movie', 2, 'Teste Legal', 18, 'rbrecci', '2026-03-25 19:51:20'),
(35, 'create', 'movie', 4, 'Teste Segundo', 18, 'rbrecci', '2026-03-25 20:07:53'),
(36, 'create', 'movie', 5, 'Teste terciario da silva gomes', 18, 'rbrecci', '2026-03-25 20:10:24'),
(37, 'delete', 'movie', 3, 'Teste da SIlva', 18, 'rbrecci', '2026-03-25 20:54:29'),
(38, 'create', 'user', 22, 'Rubinho', 18, 'rbrecci', '2026-03-29 14:10:13'),
(39, 'delete', 'user', 22, 'Rubinho', 18, 'rbrecci', '2026-03-29 14:10:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(1, 'Ação'),
(2, 'Aventura'),
(3, 'Comédia'),
(4, 'Drama'),
(5, 'Terror'),
(6, 'Suspense'),
(7, 'Romance'),
(8, 'Ficção Científica'),
(9, 'Fantasia'),
(10, 'Animação'),
(11, 'Documentário'),
(12, 'Crime'),
(13, 'Mistério'),
(14, 'Musical'),
(15, 'Guerra');

-- --------------------------------------------------------

--
-- Estrutura para tabela `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `release_yr` year(4) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `rented` tinyint(1) NOT NULL DEFAULT 0,
  `rent_start_date` date DEFAULT NULL,
  `rent_end_date` date DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `trailer_url` varchar(255) DEFAULT NULL,
  `age_rating` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `release_yr`, `duration`, `rented`, `rent_start_date`, `rent_end_date`, `poster`, `banner`, `trailer_url`, `age_rating`) VALUES
(4, 'Teste Segundo', 'teste segundo da silva uhuul testando', '2023', 180, 0, NULL, NULL, NULL, NULL, NULL, '16'),
(5, 'Teste terciario da silva gomes', 'testando o table striped do bootstrap', '1950', 200, 0, NULL, NULL, NULL, NULL, NULL, '18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `movie_genres`
--

CREATE TABLE `movie_genres` (
  `movie_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `movie_genres`
--

INSERT INTO `movie_genres` (`movie_id`, `genre_id`) VALUES
(4, 3),
(4, 7),
(4, 8),
(5, 6),
(5, 10),
(5, 12);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `type` enum('user','admin') NOT NULL DEFAULT 'user',
  `last_login_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `pass`, `type`, `last_login_date`) VALUES
(18, 'fbrecci@gmail.com', 'rbrecci', '$2y$10$RTob6c.aR/VxVKDUNJMQuOOSG3Ed7VB2R7wCM6V60riyxXzzKUgm6', 'admin', '2026-03-29 14:34:25'),
(20, 'teste123@gmail.com', 'teste uhul', '$2y$10$jfjaX476tlAztDJJc9h5PuDtI6vb.NgENAiTYp7n3rAJcJcPzGB6q', 'user', '2026-03-16 22:44:34');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD PRIMARY KEY (`movie_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de tabela `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD CONSTRAINT `movie_genres_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movie_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
