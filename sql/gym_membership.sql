-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2025 at 08:55 AM
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
(1, 'solehin', '$2a$04$Sn4deUWm5TbNJgMqLwzy3eaZJyI8Z1BZLQEkZ7s60chUtsNdBOpW2'),
(2, 'azfar', '$2a$04$HRReQNj3v31J.hgebh1GieImRheVLlCTQWV9rdVQDZQxoVWONlTxC'),
(3, 'hafizuddin', '$2a$04$OOG18bd3V6ZSZIjUKKq2Xu9B.qc1JOTs6VueAx3NicD8TptFhhUJO'),
(4, 'meor', '$2a$04$kXWmevDhLZhUFKr.sSoDbexiJQeDPZdxUKD14tLzXB3wzINdp2MuW'),
(5, 'fauzi', '$2a$04$Uu9eYbYqF.vT8xh1PMk.HezFhMyjUaQSwcZxqrSmdNydXObtbL6XG');

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
(7, 'Akmal Ali', 'male', 'inactive', 'ali.akmal@gmail.com', '$2y$10$VtTAtK1OEwD3b2UwlSR/Y.RUbmWqiJqaV/KqAA97FKxfuA2UvBhIe', '0123456789', 'No. 123, Jalan Mawar, Kuala Lumpur', '2025-01-11 05:43:23', NULL),
(8, 'Nurul Huda', 'female', 'active', 'nurul.huda@gmail.com', '$2y$10$7BEmq208rukxE/OzpnL6rOtrbNSkFa3MbcYsZa3DRsJwNH2Z2zyi.', '0198765432', 'No. 567, Taman Sentosa, Johor Bahru', '2025-01-11 05:43:53', NULL),
(9, 'Ahmad Zain', 'male', 'inactive', 'ahmad.zain@gmail.com', '$2y$10$KBAGeN48jIx2hqbUz5N2Ee8553w.I1yuRx6KIMlhpwmMJ/fFiay9O', '0182345678', 'No. 89, Jalan Impian, Shah Alam', '2025-01-11 05:44:24', NULL),
(10, 'Siti Aminah', 'female', 'active', 'siti.aminah@gmail.com', '$2y$10$4DjAU1EJ.e7zhY6FsSfj4ek5LOCdWRwJTIzvxiUsXzJ93KlC7cXrO', '0134567890', 'No. 45, Taman Maju, Melaka', '2025-01-11 05:45:08', NULL),
(11, 'Hakim Faisal', 'male', 'inactive', 'hakim.faisal@gmail.com', '$2y$10$t9IXbYHa/Mp7pwoS/4d5v./pKd8EpE0RYxc9.BQVvGHotN6xFYMMm', '0126543210', 'No. 34, Jalan Merdeka, Kota Bharu', '2025-01-11 05:53:09', NULL),
(12, 'Liyana Shahirah', 'female', 'inactive', 'liyana.shahirah@gmail.com', '$2y$10$mfRL4syLSZRRPaZpAP9P.elgp7rW0UBxUCdGTThrYawSzLKtj4pXW', '0177894561', 'No. 77, Jalan Permai, Klang', '2025-01-11 05:53:57', NULL),
(13, 'Adam Firdaus', 'male', 'inactive', 'adam.firdaus@gmail.com', '$2y$10$y93nVC507Mhd90AEZnjuW./ywDn5olGhy33FRJyrovZ1zD5xh1nvO', '0181236547', 'No. 101, Jalan Sejahtera, Ipoh', '2025-01-11 05:54:34', NULL),
(14, 'Aisyah Sofea', 'female', 'inactive', 'aisyah.sofea@gmail.com', '$2y$10$f07LK.5FCBqSmqIeGhjRXecilu/2MKd/isd6ElMHJ41E9d1q0JdTC', '0169081723', 'No. 89, Taman Bahagia, Alor Setar', '2025-01-11 05:55:05', NULL),
(15, 'Haziq Farhan', 'male', 'inactive', 'haziq.farhan@gmail.com', '$2y$10$zS8VO23G1ICOphtwKSMRve6dLLQ3GhkGXf6AzWDYlARhvFCM1M/v2', '0197896542', 'No. 33, Taman Jaya, Kuala Terengganu', '2025-01-11 05:55:33', NULL),
(16, 'Nur Amira', 'female', 'inactive', 'nur.amira@gmail.com', '$2y$10$k64bFt7Kzw5Ht9IThum9lOp6zLw.9a17.xkha65Qi3hXc2JWPpiNO', '0174503291', 'No. 67, Jalan Mawar, George Town', '2025-01-11 05:56:00', NULL),
(17, 'Fahmi Azlan', 'male', 'inactive', 'fahmi.azlan@gmail.com', '$2y$10$qM/o/phZKRCI.HG6kvtDKOFmdMJODCzUzhFQ.HlnIszOPkG5zdQXG', '0183457892', 'No. 200, Jalan Impian, Seremban', '2025-01-11 05:56:29', NULL),
(18, 'Amalina Zahra', 'female', 'inactive', 'amalina.zahra@gmail.com', '$2y$10$FZK4EuIcCsLaRocMTtOF.e3007b2GmaZUKIzyFluHqOF61wEdSuN2', '0126789451', 'No. 55, Taman Desa, Kuala Lumpur', '2025-01-11 05:57:07', NULL),
(19, 'Kamal Harith', 'male', 'inactive', 'kamal.harith@gmail.com', '$2y$10$J3w.gDZIsYHE/I4eZt2daOwx4I./30JqpIGBOEZ239YpNQM80L1we', '0195437890', 'No. 88, Taman Sentosa, Kuantan', '2025-01-11 05:57:39', NULL),
(20, 'Sofia Izzati', 'female', 'inactive', 'sofia.izzati@gmail.com', '$2y$10$zZV7niu.UNeaXXy7RVptJe1JdQD54M.e9QhYPfO8WClfDPC5qglwu', '0176549873', 'No. 123, Jalan Bahagia, Petaling Jaya', '2025-01-11 05:58:04', NULL),
(21, 'Azim Daniel', 'male', 'active', 'azim.daniel@gmail.com', '$2y$10$5JpGDoNn3ML8TUQG44geu.VV8oXuJJ2OPaO1CAHnxbjVtL.cQEI6u', '0182346598', 'No. 99, Jalan Sentosa, Klang', '2025-01-11 05:58:32', NULL),
(22, 'Zulaikha Amani', 'female', 'inactive', 'zulaikha.amani@gmail.com', '$2y$10$eSk3pG6D.kjQDB4fvSHkJu.k.Ue8utkAD1iJChHaoMcDynx/lYZ7a', '0127892346', 'No. 110, Jalan Impian, Putrajaya', '2025-01-11 05:58:59', NULL),
(23, 'Rashid Iskandar', 'male', 'inactive', 'rashid.iskandar@gmail.com', '$2y$10$.kBPpo1gpLPAFUYzeftvMuWkEfdGaBIu3u.Rc4CfIAEVIP9tw0yG6', '0196758493', 'No. 150, Jalan Maju, Johor Bahru', '2025-01-11 05:59:31', NULL),
(24, 'Hana Aqilah', 'female', 'inactive', 'hana.aqilah@gmail.com', '$2y$10$caS/KdbbVgUW2c2dUubEv.G6S1AUCqo/cpV66IsouoPvXL4FkjYMy', '0132347864', 'No. 120, Taman Permai, Langkawi', '2025-01-11 05:59:59', NULL),
(25, 'Syahir Irfan', 'male', 'inactive', 'syahir.irfan@gmail.com', '$2y$10$tlvG5XgaHkzJy9FY6ugj4enHtA.fHVfcME66dgnzI4RMts3T7uQES', '0173216987', 'No. 140, Jalan Desa, Tawau', '2025-01-11 06:00:25', NULL),
(26, 'Nadia Liyana', 'female', 'inactive', 'nadia.liyana@gmail.com', '$2y$10$zDm2cNxcjjA0AodLDpLByuuFHCM/ao6M7kg2k4cpAUGZL736rvfky', '0167894321', 'No. 101, Jalan Sejahtera, Sandakan', '2025-01-11 06:00:58', NULL);

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
(8, 7, 4, '2024-12-10', '2025-01-10', 'expired'),
(9, 8, 6, '2025-01-11', '2026-01-11', 'active'),
(10, 9, 4, '2024-11-01', '2024-12-01', 'expired'),
(11, 10, 6, '2024-10-01', '2025-10-01', 'active'),
(12, 21, 5, '2024-11-01', '2025-05-01', 'active');

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
  `admin_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in months',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `admin_id`, `name`, `price`, `duration`, `created_at`) VALUES
(4, 5, 'Basic', 25.90, 1, '2025-01-11 04:54:54'),
(5, 1, 'Pro', 155.00, 6, '2025-01-11 04:55:28'),
(6, 1, 'Premium', 329.00, 12, '2025-01-11 04:55:59');

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
(13, 7, 4, 'paid', 25.90, 'Maybank', '2024-12-10 06:57:23'),
(14, 8, 6, 'paid', 329.00, 'RHB Bank', '2025-01-11 07:09:15'),
(15, 9, 5, 'canceled', 0.00, NULL, '2025-01-11 07:36:14'),
(16, 9, 4, 'paid', 25.90, 'Bank Islam', '2024-11-01 07:37:28'),
(17, 10, 6, 'paid', 329.00, 'Bank Islam', '2024-10-01 07:41:19'),
(18, 21, 4, 'canceled', 0.00, NULL, '2025-01-11 07:45:10'),
(19, 21, 5, 'paid', 155.00, 'RHB Bank', '2024-11-01 07:45:25');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `member_packages`
--
ALTER TABLE `member_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
-- Constraints for table `packages`
--
ALTER TABLE `packages`
  ADD CONSTRAINT `packages_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`);

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
