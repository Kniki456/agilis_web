-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2023 at 03:40 PM
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
(1, 'niki456vagyok@gmail.com', 'alma123');

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
(11, '12 + 7', 19);

-- --------------------------------------------------------

--
-- Table structure for table `highscores`
--

CREATE TABLE `highscores` (
  `user_id` int(11) NOT NULL,
  `Name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `highscores`
--

INSERT INTO `highscores` (`user_id`, `Name`, `Score`) VALUES
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
(6, '16 - 9', 7);

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
  MODIFY `elso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `highscores`
--
ALTER TABLE `highscores`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `masodikszint`
--
ALTER TABLE `masodikszint`
  MODIFY `masodik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
