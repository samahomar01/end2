-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2024 at 09:36 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lab_techcare`
--

-- --------------------------------------------------------

--
-- Table structure for table `accesspoint`
--

CREATE TABLE `accesspoint` (
  `acc_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `wireless` varchar(255) NOT NULL,
  `speed` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case`
--

CREATE TABLE `case` (
  `ca_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` text NOT NULL,
  `size` int(255) NOT NULL,
  `motherboard` varchar(255) NOT NULL,
  `processor` varchar(255) NOT NULL,
  `memory` varchar(255) NOT NULL,
  `harddiskcap` text NOT NULL,
  `harddisktype` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `composed`
--

CREATE TABLE `composed` (
  `co_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` varchar(255) NOT NULL,
  `listcomponents` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id_eq` int(100) NOT NULL,
  `name` text NOT NULL,
  `brand` text NOT NULL,
  `serial_no` varchar(100) NOT NULL,
  `status` text NOT NULL,
  `manufacturLot` varchar(255) NOT NULL,
  `lo_id` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id_eq`, `name`, `brand`, `serial_no`, `status`, `manufacturLot`, `lo_id`, `type`, `is_available`) VALUES
(16, 'kb', 'yyy', '123', 'y', 'jjhj', 10, 'hhhh', 1),
(20, 'sasa', 'asf', '1234', 'y', 'asd', 10, 'Monitor', 1),
(22, 'qwe', 'eeee', '12345', 'y', 'ew', 10, 'Monitor', 1),
(24, 'hjkl', 'lkj', '789', 'y', 'hhh', 10, 'Mouse', 1),
(26, 'jjj', 'g', '567', 'y', 'f', 10, 'Mouse', 1),
(28, 'asd', 'iop', '2221', 'y', 'jkl', 10, 'Mouse', 1);

-- --------------------------------------------------------

--
-- Table structure for table `fax`
--

CREATE TABLE `fax` (
  `fa_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` varchar(255) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `speed` varchar(255) NOT NULL,
  `typeofpaper` varchar(255) NOT NULL,
  `refillcode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keyboard`
--

CREATE TABLE `keyboard` (
  `ke_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` varchar(255) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `layout` varchar(255) NOT NULL,
  `aren` text NOT NULL,
  `connectiontype` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

CREATE TABLE `labs` (
  `id` int(11) NOT NULL,
  `labName` varchar(255) NOT NULL,
  `totalDevices` int(11) NOT NULL,
  `workingDevices` int(11) DEFAULT 0,
  `nonWorkingDevices` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `labs`
--

INSERT INTO `labs` (`id`, `labName`, `totalDevices`, `workingDevices`, `nonWorkingDevices`) VALUES
(1, 'lad2', 30, 20, 10),
(2, 'lab3', 20, 10, 10);

-- --------------------------------------------------------

--
-- Table structure for table `lab_devices`
--

CREATE TABLE `lab_devices` (
  `id` int(11) NOT NULL,
  `lab_id` int(11) NOT NULL,
  `device_name` varchar(255) NOT NULL,
  `components` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `physicalLocation` varchar(100) NOT NULL,
  `publicAccess` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `name`, `physicalLocation`, `publicAccess`) VALUES
(10, 'lab1', 'home', '1'),
(11, 'lab2', 'work', '1'),
(12, 'lab3', 'jojo', '1'),
(13, 'lab4', 'aaaa', '0');

-- --------------------------------------------------------

--
-- Table structure for table `monitor`
--

CREATE TABLE `monitor` (
  `mo_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` text NOT NULL,
  `subtype` text NOT NULL,
  `size` int(255) NOT NULL,
  `maxresolution` int(255) NOT NULL,
  `coonnectiontype` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monitor`
--

INSERT INTO `monitor` (`mo_id`, `eq_id`, `type`, `subtype`, `size`, `maxresolution`, `coonnectiontype`) VALUES
(1, 22, 'Monitor', 'mo', 23, 211, 'www');

-- --------------------------------------------------------

--
-- Table structure for table `mouse`
--

CREATE TABLE `mouse` (
  `mo_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` varchar(255) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `connectiontype` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mouse`
--

INSERT INTO `mouse` (`mo_id`, `eq_id`, `type`, `subtype`, `connectiontype`) VALUES
(1, 28, 'Mouse', 'mo', 'nb');

-- --------------------------------------------------------

--
-- Table structure for table `networking`
--

CREATE TABLE `networking` (
  `net_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` varchar(255) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `networktype` varchar(255) NOT NULL,
  `networkname` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_status` tinyint(4) NOT NULL DEFAULT 0,
  `additional_data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photocopier`
--

CREATE TABLE `photocopier` (
  `ph_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` varchar(255) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `documentfeeder` varchar(255) NOT NULL,
  `maxsizepaper` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `printer`
--

CREATE TABLE `printer` (
  `pr_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` text NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `maxpapersize` varchar(255) NOT NULL,
  `refillcode` varchar(255) NOT NULL,
  `connectiontype` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projector`
--

CREATE TABLE `projector` (
  `pr_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` varchar(255) NOT NULL,
  `suptype` varchar(255) NOT NULL,
  `maxprojectionsize` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `router`
--

CREATE TABLE `router` (
  `ro_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `totalnumports` varchar(255) NOT NULL,
  `poeports` varchar(255) NOT NULL,
  `wireless` varchar(255) NOT NULL,
  `speed` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scanner`
--

CREATE TABLE `scanner` (
  `sc_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `type` varchar(255) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `maxsizetype` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `switch`
--

CREATE TABLE `switch` (
  `sw_id` int(100) NOT NULL,
  `eq_id` int(100) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  `totalnumports` varchar(255) NOT NULL,
  `poeports` varchar(255) NOT NULL,
  `foconnections` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(255) NOT NULL,
  `user_id` int(100) NOT NULL,
  `state` text NOT NULL,
  `date` date NOT NULL,
  `issue` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `user_id`, `state`, `date`, `issue`) VALUES
(1, 1, 'open', '2024-06-12', 'gggggggg'),
(2, 8, 'open', '2024-06-12', 'sssssssss'),
(3, 8, 'open', '2024-06-12', 'food'),
(4, 8, 'open', '2024-06-12', 'hi '),
(5, 8, 'open', '2024-06-14', 'ggqqz'),
(6, 8, 'open', '2024-06-18', 'computer off');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(111) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'saaaaaa', 'sssssss', '$2y$10$cnevatfHdO/gZkHTiIBmJuYFQHGOBADzA1cmYiR5yWjPLaPmvIYAW', 'User'),
(2, 'saaaaaa', 'sssssss@gmail.com', '$2y$10$Ez0CdrUfYT8cT6ijcXOkU.MNUl5MKaNSHrcdbcJHydjsuEdXlTwNS', 'User'),
(3, 'saaaaaa', 'fssss@gmail.com', '$2y$10$FeGKNDt.LBlYCUh/CVah2OjXqskpeMFczNbUKJ6Yg4DE0qEnwhOP2', 'User'),
(4, 'sara', 'sara@gmail.com', '$2y$10$XkwjGGMPW7L2BZJHeMheZ.BglM0C/KKI6jvtuvxDT/4wWnqjoAmZy', 'Technician'),
(5, 'good', 'goood', '$2y$10$zvqdRS/Om7xHPE4kloxkruJHFREKGiFe1qjfxlr.ILWUndu9yYsW.', 'Supervisor'),
(6, 'good', 'goood@gmail.com', '$2y$10$QIowx8Gw9n4wTQXmP3sxGOJcGE6NSNFMHmn0K7226qH74a6CsaA/S', 'User'),
(7, 'sondos', 'sondos@gmail.com', '$2y$10$/bvNu2/2oSxEz.5UCCOoo.YrL5.A1z6okMb7ucdiPmzURpTSYyqcS', 'User'),
(8, 'sa', 'sa', '$2y$10$Jnm3suij32AyKff1m.UEKeTD.4PgvAH.E6rXLdg.1k2.7Fu8cEdk2', 'User'),
(9, 'ra', 'ra', '$2y$10$I5hCr1sgfTe27.FlVseLaOjP4vYL78t6bp1GV.JQkmkJfbWzOwG1m', 'Technician'),
(10, 'fa', 'fa', '$2y$10$LxBvxqBIcjQCNnpFPFH/QuMUupigwZ3b15DQhNfp7u2kr79nfSw6q', 'Supervisor'),
(11, 'Ahmed', 'ahmed omar', '$2y$10$2gSto9LT2CCJH1rKrAJcounHV6gUKFzfM5RLMnGcm0M5YGW1WtC36', 'user'),
(12, 'sona', 'sona', '$2y$10$Py3g4PrrjO371o651lh9yulHxXJNotsprYJjlUQi0oSwfo/bhbgMy', 'ssssssssssss'),
(13, 'asmaa', 'asma', '$2y$10$SCdg3wQuM11ltObZm8GHAO5Vc.d1Qgmdm.GUYuN7HWeRfLMY3c1Zm', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesspoint`
--
ALTER TABLE `accesspoint`
  ADD PRIMARY KEY (`acc_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `case`
--
ALTER TABLE `case`
  ADD PRIMARY KEY (`ca_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `composed`
--
ALTER TABLE `composed`
  ADD PRIMARY KEY (`co_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id_eq`),
  ADD KEY `lo_id` (`lo_id`);

--
-- Indexes for table `fax`
--
ALTER TABLE `fax`
  ADD PRIMARY KEY (`fa_id`);

--
-- Indexes for table `keyboard`
--
ALTER TABLE `keyboard`
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_devices`
--
ALTER TABLE `lab_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_id` (`lab_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monitor`
--
ALTER TABLE `monitor`
  ADD PRIMARY KEY (`mo_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `mouse`
--
ALTER TABLE `mouse`
  ADD PRIMARY KEY (`mo_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `networking`
--
ALTER TABLE `networking`
  ADD PRIMARY KEY (`net_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id_index` (`user_id`),
  ADD KEY `timestamp_index` (`timestamp`);

--
-- Indexes for table `photocopier`
--
ALTER TABLE `photocopier`
  ADD PRIMARY KEY (`ph_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `printer`
--
ALTER TABLE `printer`
  ADD PRIMARY KEY (`pr_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `projector`
--
ALTER TABLE `projector`
  ADD PRIMARY KEY (`pr_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `router`
--
ALTER TABLE `router`
  ADD PRIMARY KEY (`ro_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `scanner`
--
ALTER TABLE `scanner`
  ADD PRIMARY KEY (`sc_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `switch`
--
ALTER TABLE `switch`
  ADD PRIMARY KEY (`sw_id`),
  ADD KEY `eq_id` (`eq_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accesspoint`
--
ALTER TABLE `accesspoint`
  MODIFY `acc_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case`
--
ALTER TABLE `case`
  MODIFY `ca_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `composed`
--
ALTER TABLE `composed`
  MODIFY `co_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id_eq` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `fax`
--
ALTER TABLE `fax`
  MODIFY `fa_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `labs`
--
ALTER TABLE `labs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lab_devices`
--
ALTER TABLE `lab_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `monitor`
--
ALTER TABLE `monitor`
  MODIFY `mo_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mouse`
--
ALTER TABLE `mouse`
  MODIFY `mo_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `networking`
--
ALTER TABLE `networking`
  MODIFY `net_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `photocopier`
--
ALTER TABLE `photocopier`
  MODIFY `ph_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `printer`
--
ALTER TABLE `printer`
  MODIFY `pr_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projector`
--
ALTER TABLE `projector`
  MODIFY `pr_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `router`
--
ALTER TABLE `router`
  MODIFY `ro_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scanner`
--
ALTER TABLE `scanner`
  MODIFY `sc_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `switch`
--
ALTER TABLE `switch`
  MODIFY `sw_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accesspoint`
--
ALTER TABLE `accesspoint`
  ADD CONSTRAINT `accesspoint_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `case`
--
ALTER TABLE `case`
  ADD CONSTRAINT `case_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `composed`
--
ALTER TABLE `composed`
  ADD CONSTRAINT `composed_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`lo_id`) REFERENCES `location` (`id`);

--
-- Constraints for table `keyboard`
--
ALTER TABLE `keyboard`
  ADD CONSTRAINT `keyboard_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `lab_devices`
--
ALTER TABLE `lab_devices`
  ADD CONSTRAINT `lab_devices_ibfk_1` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`);

--
-- Constraints for table `monitor`
--
ALTER TABLE `monitor`
  ADD CONSTRAINT `monitor_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `mouse`
--
ALTER TABLE `mouse`
  ADD CONSTRAINT `mouse_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `networking`
--
ALTER TABLE `networking`
  ADD CONSTRAINT `networking_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `photocopier`
--
ALTER TABLE `photocopier`
  ADD CONSTRAINT `photocopier_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `printer`
--
ALTER TABLE `printer`
  ADD CONSTRAINT `printer_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `projector`
--
ALTER TABLE `projector`
  ADD CONSTRAINT `projector_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `router`
--
ALTER TABLE `router`
  ADD CONSTRAINT `router_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `scanner`
--
ALTER TABLE `scanner`
  ADD CONSTRAINT `scanner_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `switch`
--
ALTER TABLE `switch`
  ADD CONSTRAINT `switch_ibfk_1` FOREIGN KEY (`eq_id`) REFERENCES `equipment` (`id_eq`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
