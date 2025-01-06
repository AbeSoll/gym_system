-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2025 at 04:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gym_membership`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `check_member_status` ()   BEGIN
    -- Mark members as inactive if all their member_packages are expired
    UPDATE members m
    SET m.status = 'inactive'
    WHERE NOT EXISTS (
        SELECT 1 FROM member_packages mp 
        WHERE mp.member_id = m.id AND mp.status = 'active'
    );

    -- Mark members as active if they have any active packages
    UPDATE members m
    SET m.status = 'active'
    WHERE EXISTS (
        SELECT 1 FROM member_packages mp 
        WHERE mp.member_id = m.id AND mp.status = 'active'
    );
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$QJcJW6Qvw8a3q1e7nOsAge6MzkKCjnm5L45wwn8DbQllbwOzpQ6FG');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `status` enum('inactive','active') DEFAULT 'inactive',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `gender`, `status`, `email`, `password`, `phone`, `address`, `created_at`, `profile_picture`) VALUES
(4, 'AHMAD', 'male', 'inactive', 'solehinahmad954@gmail.com', '$2y$10$uYF8/jJU5Z9v1yL9GgfcoeVpA6cyjUEQCrnAxigjDm7b/lt9Lsece', '0177587549', 'NO 143 FELCRA KAMPUNG MELAYU BATU 4 PALOH', '2025-01-04 15:14:56', NULL),
(5, 'ALYSHA', 'female', 'inactive', 'alysha@gmail.com', '$2y$10$tlX4uKb.hyn6CJDRGL3PSeBek9dNvQUz6FQNxVK.u3hEzrtR5FwtW', '01111111111', 'No 143 felcra keramat 86600 kluang johor', '2025-01-05 16:37:19', NULL),
(6, 'AMRI', 'male', 'active', 'amri123@gmail.com', '$2y$10$FrVzN6X4IR2NwNJ/bjNNzuoWA5KyDxcDnLFpR/2bLqP1XKrlC2OSW', '0125632541', 'No 123 Jalan Mawar Kulai Indah, Bandar Tenggara, 86000 Kluang Johor', '2025-01-05 19:09:11', '677b3b531cef4_1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `member_packages`
--

CREATE TABLE `member_packages` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_packages`
--

INSERT INTO `member_packages` (`id`, `member_id`, `package_id`, `start_date`, `end_date`, `status`) VALUES
(7, 6, 2, '2025-01-06', '2026-01-06', 'active');

--
-- Triggers `member_packages`
--
DELIMITER $$
CREATE TRIGGER `update_member_status` AFTER UPDATE ON `member_packages` FOR EACH ROW BEGIN
    IF NEW.status = 'expired' THEN
        UPDATE members 
        SET status = 'inactive' 
        WHERE id = NEW.member_id;
    ELSEIF NEW.status = 'active' THEN
        UPDATE members 
        SET status = 'active' 
        WHERE id = NEW.member_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in months',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `price`, `duration`, `created_at`) VALUES
(1, 'Basic', 30.00, 1, '2025-01-04 17:33:46'),
(2, 'Premium', 350.00, 12, '2025-01-04 17:34:39'),
(3, 'Pro', 180.00, 6, '2025-01-04 17:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `payment_status` enum('paid','unpaid','canceled') NOT NULL DEFAULT 'unpaid',
  `amount` decimal(10,2) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `member_id`, `package_id`, `payment_status`, `amount`, `bank_name`, `payment_date`) VALUES
(11, 6, 2, 'canceled', 0.00, NULL, '2025-01-06 02:52:35'),
(12, 6, 2, 'paid', 350.00, 'RHB Bank', '2025-01-06 02:53:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `member_packages`
--
ALTER TABLE `member_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `package_id` (`package_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `member_packages`
--
ALTER TABLE `member_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `member_packages`
--
ALTER TABLE `member_packages`
  ADD CONSTRAINT `member_packages_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_packages_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `update_member_status_event` ON SCHEDULE EVERY 1 DAY STARTS '2025-01-06 02:58:15' ON COMPLETION NOT PRESERVE ENABLE DO CALL check_member_status()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
