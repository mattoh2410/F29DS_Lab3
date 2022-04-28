-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server-1
-- Generation Time: Jan 16, 2022 at 03:50 PM
-- Server version: 10.3.21-MariaDB
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- --------------------------------------------------------

--
-- Table structure for table `sdStaffNames`
--

CREATE TABLE `sdStaffNames` (
  `recid` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `userID` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `username` varchar(12) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `IP` bigint(12) UNSIGNED NOT NULL DEFAULT 0,
  `sessionID` varchar(48) NOT NULL DEFAULT '',
  `fullUsername` varchar(30) NOT NULL DEFAULT '',
  `userType` set('root','admin','user','dev','guest') NOT NULL DEFAULT 'user',
  `email` varchar(60) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sdStaffNames`
--

INSERT INTO `sdStaffNames` (`recid`, `userID`, `username`, `password`, `IP`, `sessionID`, `fullUsername`, `userType`, `email`) VALUES
(2, 3, 'lisa', '3dfc26017f80ae27', 2311261737, '8u2gl9', 'Lisa Rogers', 'user', 'l.j.rogers@hw.ac.uk'),
(3, 5, 'manager', '3f3e4305492911c1', 35207734, '8h1pi3', 'Main Manager', 'admin', 'manager@hw.ac.uk'),
(4, 6, 'guest', '57510426775c5b0f', 1544851823, '9h9fy5', 'Guest User', 'guest', ''),
(8, 10, 'jennifer', '5371899d0e2d62a2', 2160437986, '7z2pw0', 'Jennifer Robble', 'user', 'jennie@hu.edu'),
(9, 11, 'isi3', '464a6d2841d02cd9', 2311261806, '6y9mc0', 'Idris Al-skloul Ibrahim', 'dev', 'I.S.Ibrahim@hw.ac.uk'),
(11, 13, 'developer', '510724f65424f05c', 35362503, '7o9rk0', 'Callum Stewart', 'admin', 'csw1@hw.ac.uk');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sdStaffNames`
--
ALTER TABLE `sdStaffNames`
  ADD UNIQUE KEY `recid` (`recid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

