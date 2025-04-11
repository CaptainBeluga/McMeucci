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
-- Struttura della tabella `counter`
--

CREATE TABLE `counter` (
  `id` int(11) NOT NULL,
  `maxOrdini` int(11) NOT NULL,
  `orderNumber` int(11) NOT NULL,
  `maxProduct` int(11) NOT NULL,
  `maxDiscount` int(3) NOT NULL,
  `openTime` int(11) NOT NULL,
  `closeTime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `counter`
--

INSERT INTO `counter` (`id`, `maxOrdini`, `orderNumber`, `maxProduct`, `maxDiscount`, `openTime`, `closeTime`) VALUES
(0, 5, 1, 15, 100, 16, 21);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `counter`
--
ALTER TABLE `counter`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `counter`
--
ALTER TABLE `counter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
