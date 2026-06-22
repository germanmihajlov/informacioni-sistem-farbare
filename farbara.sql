-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2026 at 05:08 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `farbara`
--

-- --------------------------------------------------------

--
-- Table structure for table `dobavljac`
--

CREATE TABLE `dobavljac` (
  `id_dobavljaca` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `telefon` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dobavljac`
--

INSERT INTO `dobavljac` (`id_dobavljaca`, `naziv`, `telefon`, `email`) VALUES
(1, 'Dulux Srbija', '011123456', 'kontakt@dulux.rs'),
(2, 'Jub Boje', '011654321', 'prodaja@jub.rs');

-- --------------------------------------------------------

--
-- Table structure for table `faktura`
--

CREATE TABLE `faktura` (
  `id_fakture` int(11) NOT NULL,
  `id_porudzbine` int(11) NOT NULL,
  `datum` date NOT NULL,
  `nacin_placanja` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faktura`
--

INSERT INTO `faktura` (`id_fakture`, `id_porudzbine`, `datum`, `nacin_placanja`) VALUES
(1, 1, '2025-03-02', 'Gotovina'),
(2, 26, '2026-01-08', 'faktura'),
(3, 27, '2026-01-08', 'faktura'),
(4, 29, '2026-01-08', 'faktura'),
(5, 30, '2026-01-09', 'faktura'),
(6, 31, '2026-01-09', 'faktura'),
(7, 32, '2026-01-09', 'faktura'),
(8, 33, '2026-01-09', 'faktura');

-- --------------------------------------------------------

--
-- Table structure for table `kategorija_proizvoda`
--

CREATE TABLE `kategorija_proizvoda` (
  `id_kategorije` int(11) NOT NULL,
  `naziv_kategorije` varchar(100) NOT NULL,
  `opis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorija_proizvoda`
--

INSERT INTO `kategorija_proizvoda` (`id_kategorije`, `naziv_kategorije`, `opis`) VALUES
(1, 'Unutrašnje boje', 'Boje za zidove u zatvorenom prostoru'),
(2, 'Spoljašnje boje', 'Fasadne i spoljašnje boje'),
(3, 'Lakovi', 'Lakovi za drvo i metal'),
(4, 'Prateća oprema', 'Valjci, četke, razređivači');

-- --------------------------------------------------------

--
-- Table structure for table `kupac`
--

CREATE TABLE `kupac` (
  `id_kupca` int(11) NOT NULL,
  `naziv_firme` varchar(100) NOT NULL,
  `telefon` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kupac`
--

INSERT INTO `kupac` (`id_kupca`, `naziv_firme`, `telefon`, `email`, `adresa`) VALUES
(1, 'MAGIC WALL\r\n', '062111222', 'magicwall@gmail.com', 'Beograd, Mite Ruzica 10'),
(2, 'MPJ-COLOR', '063333444', 'mpjcolor@gmail.com', 'Novi Sad, Obalskih radnika 4'),
(3, 'Art & Decor', '061222334', 'mitrogol@gmai.com', 'Kaludjerica, Vojvode Stepe Stepanovica 14'),
(5, 'Gotovinski kupac', 'N/A', 'N/A', 'N/A');

-- --------------------------------------------------------

--
-- Table structure for table `nabavka`
--

CREATE TABLE `nabavka` (
  `id_nabavke` int(11) NOT NULL,
  `id_dobavljaca` int(11) NOT NULL,
  `id_zaposlenog` int(11) NOT NULL,
  `datum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nabavka`
--

INSERT INTO `nabavka` (`id_nabavke`, `id_dobavljaca`, `id_zaposlenog`, `datum`) VALUES
(1, 1, 2, '2025-03-01'),
(2, 2, 1, '2026-01-09'),
(3, 1, 1, '2026-01-09');

-- --------------------------------------------------------

--
-- Table structure for table `porudzbina`
--

CREATE TABLE `porudzbina` (
  `id_porudzbine` int(11) NOT NULL,
  `id_kupca` int(11) NOT NULL,
  `id_zaposlenog` int(11) NOT NULL,
  `datum` date NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `porudzbina`
--

INSERT INTO `porudzbina` (`id_porudzbine`, `id_kupca`, `id_zaposlenog`, `datum`, `status`) VALUES
(1, 1, 1, '2025-03-02', 'Završena'),
(2, 5, 1, '2026-01-07', 'stornirana'),
(3, 2, 1, '2026-01-07', 'završena'),
(4, 3, 1, '2026-01-07', 'završena'),
(5, 1, 1, '2026-01-07', 'završena'),
(6, 5, 1, '2026-01-07', 'završena'),
(7, 5, 1, '2026-01-07', 'završena'),
(8, 5, 1, '2026-01-07', 'završena'),
(9, 5, 1, '2026-01-08', 'u toku'),
(10, 2, 1, '2026-01-08', 'završena'),
(11, 1, 1, '2026-01-08', 'završena'),
(12, 5, 1, '2026-01-08', 'u toku'),
(13, 5, 1, '2026-01-08', 'završena'),
(14, 2, 1, '2026-01-08', 'završena'),
(15, 5, 1, '2026-01-08', 'završena'),
(16, 2, 1, '2026-01-08', 'završena'),
(17, 5, 1, '2026-01-08', 'završena'),
(18, 2, 1, '2026-01-08', 'završena'),
(19, 2, 1, '2026-01-08', 'završena'),
(20, 5, 1, '2026-01-08', 'završena'),
(21, 2, 1, '2026-01-08', 'završena'),
(22, 3, 1, '2026-01-08', 'završena'),
(23, 2, 1, '2026-01-08', 'završena'),
(24, 2, 1, '2026-01-08', 'završena'),
(25, 5, 1, '2026-01-08', 'završena'),
(26, 1, 1, '2026-01-08', 'završena'),
(27, 1, 1, '2026-01-08', 'završena'),
(28, 5, 1, '2026-01-08', 'završena'),
(29, 1, 1, '2026-01-08', 'završena'),
(30, 1, 1, '2026-01-09', 'završena'),
(31, 2, 1, '2026-01-09', 'završena'),
(32, 2, 1, '2026-01-09', 'završena'),
(33, 1, 1, '2026-01-09', 'završena');

-- --------------------------------------------------------

--
-- Table structure for table `proizvod`
--

CREATE TABLE `proizvod` (
  `id_proizvoda` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `tip` varchar(50) DEFAULT NULL,
  `boja` varchar(50) DEFAULT NULL,
  `zapremina` decimal(10,2) DEFAULT NULL,
  `cena` decimal(10,2) NOT NULL,
  `id_kategorije` int(11) NOT NULL,
  `kolicina_na_lageru` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proizvod`
--

INSERT INTO `proizvod` (`id_proizvoda`, `naziv`, `tip`, `boja`, `zapremina`, `cena`, `id_kategorije`, `kolicina_na_lageru`) VALUES
(1, 'Dulux EasyCare', 'Disperzija', 'Bela', 5.00, 4100.00, 1, 120),
(2, 'Jub Jupol Gold', 'Disperzija', 'Bela', 10.00, 7200.00, 1, 7),
(3, 'Dulux Fasada', 'Fasadna boja', 'Siva', 15.00, 9800.00, 2, 0),
(4, 'Sadolin Lak', 'Lak za drvo', 'Providan', 2.50, 3200.00, 3, 26),
(5, 'Valjak Profi', 'Alat', NULL, NULL, 850.00, 4, 0),
(6, 'Maxipol', NULL, NULL, NULL, 3000.00, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reklamacija`
--

CREATE TABLE `reklamacija` (
  `id_reklamacije` int(11) NOT NULL,
  `id_porudzbine` int(11) NOT NULL,
  `datum` date NOT NULL,
  `opis` text NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reklamacija`
--

INSERT INTO `reklamacija` (`id_reklamacije`, `id_porudzbine`, `datum`, `opis`, `status`) VALUES
(1, 1, '2025-03-05', 'Boja nije odgovarala nijansi sa uzorka', 'U obradi');

-- --------------------------------------------------------

--
-- Table structure for table `stavka_nabavke`
--

CREATE TABLE `stavka_nabavke` (
  `id_stavke` int(11) NOT NULL,
  `id_nabavke` int(11) NOT NULL,
  `id_proizvoda` int(11) NOT NULL,
  `kolicina` int(11) NOT NULL,
  `nabavna_cena` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stavka_nabavke`
--

INSERT INTO `stavka_nabavke` (`id_stavke`, `id_nabavke`, `id_proizvoda`, `kolicina`, `nabavna_cena`) VALUES
(1, 1, 1, 20, 3800.00),
(2, 1, 3, 10, 8200.00),
(3, 2, 1, 15, 200.00),
(4, 3, 1, 100, 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `stavka_porudzbine`
--

CREATE TABLE `stavka_porudzbine` (
  `id_stavke` int(11) NOT NULL,
  `id_porudzbine` int(11) NOT NULL,
  `id_proizvoda` int(11) NOT NULL,
  `kolicina` int(11) NOT NULL,
  `cena` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stavka_porudzbine`
--

INSERT INTO `stavka_porudzbine` (`id_stavke`, `id_porudzbine`, `id_proizvoda`, `kolicina`, `cena`) VALUES
(1, 1, 1, 2, 4500.00),
(2, 1, 5, 1, 850.00),
(3, 2, 1, 2, 4200.00),
(5, 3, 1, 3, 4200.00),
(7, 4, 6, 2, 3000.00),
(8, 5, 6, 2, 3000.00),
(9, 6, 1, 1, 4200.00),
(10, 7, 6, 1, 3000.00),
(11, 7, 1, 1, 4200.00),
(12, 8, 1, 1, 4100.00),
(13, 8, 6, 1, 3000.00),
(14, 10, 1, 1, 4100.00),
(15, 10, 6, 1, 3000.00),
(16, 11, 1, 2, 4100.00),
(17, 11, 6, 1, 3000.00),
(18, 13, 6, 2, 3000.00),
(19, 13, 1, 2, 4100.00),
(20, 14, 6, 1, 3000.00),
(21, 14, 1, 1, 4100.00),
(22, 15, 1, 1, 4100.00),
(23, 16, 1, 1, 4100.00),
(24, 16, 6, 1, 3000.00),
(25, 16, 1, 1, 4100.00),
(26, 17, 1, 1, 4100.00),
(27, 18, 6, 1, 3000.00),
(28, 19, 6, 1, 3000.00),
(29, 20, 6, 1, 3000.00),
(30, 21, 6, 1, 3000.00),
(31, 22, 2, 1, 7200.00),
(32, 22, 4, 1, 3200.00),
(33, 23, 1, 1, 4100.00),
(34, 23, 2, 1, 7200.00),
(35, 24, 1, 1, 4100.00),
(36, 24, 2, 1, 7200.00),
(37, 25, 1, 1, 4100.00),
(38, 25, 2, 1, 7200.00),
(39, 26, 1, 1, 4100.00),
(40, 26, 2, 1, 7200.00),
(41, 27, 1, 1, 4100.00),
(43, 27, 2, 1, 7200.00),
(44, 28, 1, 1, 4100.00),
(45, 29, 1, 1, 4100.00),
(46, 30, 1, 1, 4100.00),
(47, 31, 1, 1, 4100.00),
(48, 31, 2, 1, 7200.00),
(49, 32, 2, 1, 7200.00),
(50, 33, 1, 1, 4100.00);

-- --------------------------------------------------------

--
-- Table structure for table `zaposleni`
--

CREATE TABLE `zaposleni` (
  `id_zaposlenog` int(11) NOT NULL,
  `ime_prezime_z` varchar(100) NOT NULL,
  `uloga` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `lozinka` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zaposleni`
--

INSERT INTO `zaposleni` (`id_zaposlenog`, `ime_prezime_z`, `uloga`, `email`, `lozinka`) VALUES
(1, 'Marko Marković', 'Prodavac', 'marko.markovic@gmail.com', '123'),
(2, 'Jelena Stanic', 'Administrator', 'jelena.stanic@gmail.com', '1234'),
(3, 'Predrag Bubalic', 'Prodavac', 'predragbbb@hotmail.com', '12345');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dobavljac`
--
ALTER TABLE `dobavljac`
  ADD PRIMARY KEY (`id_dobavljaca`);

--
-- Indexes for table `faktura`
--
ALTER TABLE `faktura`
  ADD PRIMARY KEY (`id_fakture`),
  ADD UNIQUE KEY `id_porudzbine` (`id_porudzbine`);

--
-- Indexes for table `kategorija_proizvoda`
--
ALTER TABLE `kategorija_proizvoda`
  ADD PRIMARY KEY (`id_kategorije`);

--
-- Indexes for table `kupac`
--
ALTER TABLE `kupac`
  ADD PRIMARY KEY (`id_kupca`);

--
-- Indexes for table `nabavka`
--
ALTER TABLE `nabavka`
  ADD PRIMARY KEY (`id_nabavke`),
  ADD KEY `id_dobavljaca` (`id_dobavljaca`),
  ADD KEY `id_zaposlenog` (`id_zaposlenog`);

--
-- Indexes for table `porudzbina`
--
ALTER TABLE `porudzbina`
  ADD PRIMARY KEY (`id_porudzbine`),
  ADD KEY `id_kupca` (`id_kupca`),
  ADD KEY `id_zaposlenog` (`id_zaposlenog`);

--
-- Indexes for table `proizvod`
--
ALTER TABLE `proizvod`
  ADD PRIMARY KEY (`id_proizvoda`),
  ADD KEY `id_kategorije` (`id_kategorije`);

--
-- Indexes for table `reklamacija`
--
ALTER TABLE `reklamacija`
  ADD PRIMARY KEY (`id_reklamacije`),
  ADD KEY `id_porudzbine` (`id_porudzbine`);

--
-- Indexes for table `stavka_nabavke`
--
ALTER TABLE `stavka_nabavke`
  ADD PRIMARY KEY (`id_stavke`),
  ADD KEY `id_nabavke` (`id_nabavke`),
  ADD KEY `id_proizvoda` (`id_proizvoda`);

--
-- Indexes for table `stavka_porudzbine`
--
ALTER TABLE `stavka_porudzbine`
  ADD PRIMARY KEY (`id_stavke`),
  ADD KEY `id_porudzbine` (`id_porudzbine`),
  ADD KEY `id_proizvoda` (`id_proizvoda`);

--
-- Indexes for table `zaposleni`
--
ALTER TABLE `zaposleni`
  ADD PRIMARY KEY (`id_zaposlenog`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dobavljac`
--
ALTER TABLE `dobavljac`
  MODIFY `id_dobavljaca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faktura`
--
ALTER TABLE `faktura`
  MODIFY `id_fakture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kategorija_proizvoda`
--
ALTER TABLE `kategorija_proizvoda`
  MODIFY `id_kategorije` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kupac`
--
ALTER TABLE `kupac`
  MODIFY `id_kupca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nabavka`
--
ALTER TABLE `nabavka`
  MODIFY `id_nabavke` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `porudzbina`
--
ALTER TABLE `porudzbina`
  MODIFY `id_porudzbine` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `proizvod`
--
ALTER TABLE `proizvod`
  MODIFY `id_proizvoda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reklamacija`
--
ALTER TABLE `reklamacija`
  MODIFY `id_reklamacije` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stavka_nabavke`
--
ALTER TABLE `stavka_nabavke`
  MODIFY `id_stavke` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stavka_porudzbine`
--
ALTER TABLE `stavka_porudzbine`
  MODIFY `id_stavke` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `zaposleni`
--
ALTER TABLE `zaposleni`
  MODIFY `id_zaposlenog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `faktura`
--
ALTER TABLE `faktura`
  ADD CONSTRAINT `faktura_ibfk_1` FOREIGN KEY (`id_porudzbine`) REFERENCES `porudzbina` (`id_porudzbine`) ON UPDATE CASCADE;

--
-- Constraints for table `nabavka`
--
ALTER TABLE `nabavka`
  ADD CONSTRAINT `nabavka_ibfk_1` FOREIGN KEY (`id_dobavljaca`) REFERENCES `dobavljac` (`id_dobavljaca`) ON UPDATE CASCADE,
  ADD CONSTRAINT `nabavka_ibfk_2` FOREIGN KEY (`id_zaposlenog`) REFERENCES `zaposleni` (`id_zaposlenog`) ON UPDATE CASCADE;

--
-- Constraints for table `porudzbina`
--
ALTER TABLE `porudzbina`
  ADD CONSTRAINT `porudzbina_ibfk_1` FOREIGN KEY (`id_kupca`) REFERENCES `kupac` (`id_kupca`) ON UPDATE CASCADE,
  ADD CONSTRAINT `porudzbina_ibfk_2` FOREIGN KEY (`id_zaposlenog`) REFERENCES `zaposleni` (`id_zaposlenog`) ON UPDATE CASCADE;

--
-- Constraints for table `proizvod`
--
ALTER TABLE `proizvod`
  ADD CONSTRAINT `proizvod_ibfk_1` FOREIGN KEY (`id_kategorije`) REFERENCES `kategorija_proizvoda` (`id_kategorije`) ON UPDATE CASCADE;

--
-- Constraints for table `reklamacija`
--
ALTER TABLE `reklamacija`
  ADD CONSTRAINT `reklamacija_ibfk_1` FOREIGN KEY (`id_porudzbine`) REFERENCES `porudzbina` (`id_porudzbine`) ON UPDATE CASCADE;

--
-- Constraints for table `stavka_nabavke`
--
ALTER TABLE `stavka_nabavke`
  ADD CONSTRAINT `stavka_nabavke_ibfk_1` FOREIGN KEY (`id_nabavke`) REFERENCES `nabavka` (`id_nabavke`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stavka_nabavke_ibfk_2` FOREIGN KEY (`id_proizvoda`) REFERENCES `proizvod` (`id_proizvoda`) ON UPDATE CASCADE;

--
-- Constraints for table `stavka_porudzbine`
--
ALTER TABLE `stavka_porudzbine`
  ADD CONSTRAINT `stavka_porudzbine_ibfk_1` FOREIGN KEY (`id_porudzbine`) REFERENCES `porudzbina` (`id_porudzbine`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stavka_porudzbine_ibfk_2` FOREIGN KEY (`id_proizvoda`) REFERENCES `proizvod` (`id_proizvoda`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
