-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2023 at 05:50 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agilis`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `password`) VALUES
(1, 'rapidmath621@gmail.com', 'alma123');

-- --------------------------------------------------------

--
-- Table structure for table `elsoszint`
--

CREATE TABLE `elsoszint` (
  `elso` int(11) NOT NULL,
  `feladat` varchar(255) NOT NULL,
  `megoldas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `elsoszint`
--

INSERT INTO `elsoszint` (`elso`, `feladat`, `megoldas`) VALUES
(1, '2 + 2', 4),
(2, '8 + 8', 16),
(3, '1 + 3', 4),
(4, '6 + 7', 13),
(5, '9 + 4', 13),
(6, '5 + 9', 14),
(7, '3 + 4', 7),
(8, '2 + 8', 10),
(9, '15 + 3', 18),
(10, '6 + 9', 15),
(11, '12 + 7', 19),
(12, '15 + 9', 24),
(13, '12 + 13\r\n', 25),
(14, '14 + 7', 21),
(15, '5 + 6', 11),
(16, '21 + 8', 29),
(17, '25 + 2', 27),
(18, '52 + 4', 56),
(19, '1 + 45', 46),
(20, '17 + 6', 23),
(21, '32 + 5', 37),
(22, '12 + 13', 25),
(23, '40 + 8', 48),
(24, '65 + 1', 66),
(25, '42 + 7', 49),
(26, '64 + 9', 73),
(27, '99 + 1', 100),
(28, '83 + 7', 90),
(29, '72 + 5', 77),
(30, '10 + 2', 12),
(31, '28 + 4', 32),
(32, '59 + 8', 67),
(33, '11 + 5', 16),
(34, '22 + 3', 25),
(35, '41 + 9', 50),
(36, '53 + 4', 57),
(37, '2 + 35', 37),
(38, '9 + 19', 28),
(39, '22 + 9', 31),
(40, '31 + 14', 15),
(41, '45 + 6', 51),
(42, '3 + 48', 51),
(43, '10 + 75', 85),
(44, '15 + 7', 22),
(45, '48 + 7', 55),
(46, '61 + 8', 69),
(47, '4 + 55', 59),
(48, '36 + 7', 43);

-- --------------------------------------------------------

--
-- Table structure for table `harmadikszint`
--

CREATE TABLE `harmadikszint` (
  `harmadik` int(11) NOT NULL,
  `feladat` varchar(255) NOT NULL,
  `megoldas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `harmadikszint`
--

INSERT INTO `harmadikszint` (`harmadik`, `feladat`, `megoldas`) VALUES
(1, '5 * 5', 25),
(2, '2 * 2', 4),
(3, '3 * 4', 12),
(4, '1 * 122', 122),
(5, '0 * 4', 0),
(6, '3 * 3', 9),
(7, '2 * 5', 10),
(8, '10 * 10', 100),
(9, '12 * 2', 24),
(10, '13 * 3', 39),
(11, '2 * 7', 14),
(12, '15 * 2', 30),
(13, '8 * 8', 64),
(14, '7 * 7', 49),
(15, '3 * 15', 45),
(16, '2 * 6', 12),
(17, '2 * 8', 16),
(18, '21 * 3', 63),
(19, '2 * 19', 38),
(20, '40 * 2', 80),
(21, '16 * 2', 32),
(22, '14 * 3', 42),
(23, '7 * 9', 63),
(24, '6 * 6', 36),
(25, '12 * 3', 36),
(26, '20 * 8', 160),
(27, '11 * 11', 121),
(28, '50 * 3', 150),
(29, '22 * 4', 88),
(30, '3 * 24', 72),
(31, '4 * 12', 48),
(32, '6 * 12', 72),
(33, '2 * 28', 56),
(34, '10 * 38', 380),
(35, '12 * 12', 144),
(36, '22 * 21', 462),
(37, '13 * 13', 169),
(38, '45 * 4', 180),
(39, '52 * 4', 208),
(40, '100025 * 0', 0);

-- --------------------------------------------------------

--
-- Table structure for table `highscores`
--

CREATE TABLE `highscores` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `highscores`
--

INSERT INTO `highscores` (`user_id`, `user_name`, `Score`) VALUES
(1, 'User1', 130),
(2, 'Alma3', 330),
(3, 'Csillag23', 430),
(4, 'Pista', 40),
(5, 'Pika', 15),
(6, 'Paka', 550);

-- --------------------------------------------------------

--
-- Table structure for table `masodikszint`
--

CREATE TABLE `masodikszint` (
  `masodik` int(11) NOT NULL,
  `feladat` varchar(255) NOT NULL,
  `megoldas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `masodikszint`
--

INSERT INTO `masodikszint` (`masodik`, `feladat`, `megoldas`) VALUES
(1, '5 - 3', 2),
(2, '9 - 8', 1),
(3, '6 - 1', 5),
(4, '7 - 5', 2),
(5, '18 - 8', 10),
(6, '16 - 9', 7),
(7, '16 - 2', 14),
(8, '33 - 15', 17),
(9, '12 - 3', 9),
(10, '56 - 4', 52),
(11, '19 - 9', 10),
(12, '45 - 7', 38),
(13, '55 - 8', 47),
(14, '52 - 7', 45),
(15, '22 - 11', 11),
(16, '39 - 12', 27),
(17, '56 - 5', 51),
(18, '65 - 3', 62),
(19, '22 - 7', 15),
(20, '85 - 12', 73),
(21, '77 - 5', 72),
(22, '70 - 3', 67),
(23, '52 - 6', 46),
(24, '92 - 7', 85),
(25, '32 - 5', 27),
(26, '76 - 4', 72),
(27, '41 - 9', 32),
(28, '9 - 2', 7),
(29, '95 - 3', 92),
(30, '17 - 8', 9),
(31, '23 - 10', 13),
(32, '35 - 17', 18),
(33, '64 - 13', 51),
(34, '72 - 25', 47),
(35, '85 - 56', 29),
(36, '44 - 23', 21),
(37, '51 - 14', 37),
(38, '29 - 15', 14),
(39, '58 - 39', 19),
(40, '67 - 28', 39);

-- --------------------------------------------------------

--
-- Table structure for table `negyedikszint`
--

CREATE TABLE `negyedikszint` (
  `negyedik` int(11) NOT NULL,
  `feladat` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `megoldas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `negyedikszint`
--

INSERT INTO `negyedikszint` (`negyedik`, `feladat`, `megoldas`) VALUES
(1, '4 / 2', 2),
(2, '16 / 4', 4),
(3, '3 / 3', 1),
(4, '10 / 1', 10),
(5, '15 / 3', 5),
(6, '22 / 2', 11),
(7, '32 / 16', 2),
(8, '15/3', 5),
(9, '18/6', 3),
(10, '20/10', 2),
(11, '52/2', 26),
(12, '40/2', 20),
(13, '16/8', 2),
(14, '72/8', 9),
(15, '81/9', 9),
(16, '60/3', 20),
(17, '25/5', 5),
(18, '32/2', 16),
(19, '45/9', 5),
(20, '100/50', 2),
(21, '56/8', 7),
(22, '34/2', 17),
(23, '72/24', 3),
(24, '144/12', 12),
(25, '60/4', 15),
(26, '36/6', 6),
(27, '150/50', 3),
(28, '169/13', 13),
(29, '32/8', 4),
(30, '81/9', 9),
(31, '88/8', 10),
(32, '102/3', 34),
(33, '75*5', 15),
(34, '93/3', 31),
(35, '121/11', 11),
(36, '225/5', 45),
(37, '133/7', 19),
(38, '196/14', 14),
(39, '155/5', 31),
(40, '210/15', 14);

-- --------------------------------------------------------

--
-- Table structure for table `otodikszint`
--

CREATE TABLE `otodikszint` (
  `otodik` int(11) NOT NULL,
  `feladat` varchar(255) NOT NULL,
  `megoldas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `otodikszint`
--

INSERT INTO `otodikszint` (`otodik`, `feladat`, `megoldas`) VALUES
(1, '12+3', 15),
(2, '15-9', 6),
(3, '58-39', 19),
(4, '26+19', 45),
(5, '65+50', 115),
(6, '148-75', 73),
(7, '63-18', 45),
(8, '78+31', 109),
(9, '72-16', 56),
(10, '98+42', 140),
(11, '43+17', 60),
(12, '94-37', 57),
(13, '36+48', 84),
(14, '111-80', 31),
(15, '25+79', 104),
(16, '60-25', 35),
(17, '78+48', 126),
(18, '90-67', 23),
(19, '13+21', 34),
(20, '105-86', 19),
(21, '15*15', 225),
(22, '36/12', 3),
(23, '3*90', 270),
(24, '120/6', 20),
(25, '54*2', 108),
(26, '156/3', 52),
(27, '48*2', 96),
(28, '99/11', 9),
(29, '12*12', 144),
(30, '196/14', 14),
(31, '32*3', 96),
(32, '128/4', 32),
(33, '81*2', 162),
(34, '200/8', 25),
(35, '125*0', 0),
(36, '156/3', 52),
(37, '12*6', 72),
(38, '175/25', 7),
(39, '33*4', 132),
(40, '1000/0', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `elsoszint`
--
ALTER TABLE `elsoszint`
  ADD PRIMARY KEY (`elso`);

--
-- Indexes for table `harmadikszint`
--
ALTER TABLE `harmadikszint`
  ADD PRIMARY KEY (`harmadik`);

--
-- Indexes for table `highscores`
--
ALTER TABLE `highscores`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `masodikszint`
--
ALTER TABLE `masodikszint`
  ADD PRIMARY KEY (`masodik`);

--
-- Indexes for table `negyedikszint`
--
ALTER TABLE `negyedikszint`
  ADD PRIMARY KEY (`negyedik`);

--
-- Indexes for table `otodikszint`
--
ALTER TABLE `otodikszint`
  ADD PRIMARY KEY (`otodik`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `elsoszint`
--
ALTER TABLE `elsoszint`
  MODIFY `elso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `harmadikszint`
--
ALTER TABLE `harmadikszint`
  MODIFY `harmadik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `highscores`
--
ALTER TABLE `highscores`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `masodikszint`
--
ALTER TABLE `masodikszint`
  MODIFY `masodik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `negyedikszint`
--
ALTER TABLE `negyedikszint`
  MODIFY `negyedik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `otodikszint`
--
ALTER TABLE `otodikszint`
  MODIFY `otodik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
