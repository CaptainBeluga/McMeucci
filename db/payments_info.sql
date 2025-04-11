-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 11, 2025 alle 17:11
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
-- Struttura della tabella `payments_info`
--

CREATE TABLE `payments_info` (
  `id` int(11) NOT NULL,
  `payID` varchar(30) NOT NULL,
  `saleID` varchar(17) NOT NULL,
  `email` text NOT NULL,
  `firstName` text NOT NULL,
  `lastName` text NOT NULL,
  `payerID` varchar(13) NOT NULL,
  `countryCode` varchar(2) NOT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `payments_info`
--

INSERT INTO `payments_info` (`id`, `payID`, `saleID`, `email`, `firstName`, `lastName`, `payerID`, `countryCode`, `status`) VALUES
(1, 'PAYID-', 'saleID', 'email@example.com', 'Mr', 'White', 'payerID', 'IT', 'approved - completed');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `payments_info`
--
ALTER TABLE `payments_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `payments_info`
--
ALTER TABLE `payments_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
