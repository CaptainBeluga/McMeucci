-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 11, 2025 alle 17:08
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mcmeucci`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` float NOT NULL,
  `discount` int(11) NOT NULL DEFAULT 0,
  `onSale` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `menu`
--

INSERT INTO `menu` (`id`, `name`, `price`, `discount`, `onSale`) VALUES
(1, 'Panino Bresaola', 2.2, 0, 1),
(2, 'Crispy McBert', 89.94, 95, 1),
(3, 'Prosciutto Crudo', 1.99, 0, 1),
(4, 'Meucci Burger', 2.5, 30, 0),
(5, 'Panino Salame', 1.5, 15, 1),
(6, 'Panino Cotoletta', 1.99, 0, 1),
(7, 'Panino Speck', 1.05, 0, 1),
(8, 'Panino Mortadella', 1.39, 0, 0),
(9, 'Panino JTacchino', 1.78, 0, 1),
(10, 'Tramezzino Tonno', 1.21, 0, 1),
(11, 'Coca(ina) Plus', 0.95, 15, 1),
(12, 'Orange Pop', 89.94, 90, 1);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
