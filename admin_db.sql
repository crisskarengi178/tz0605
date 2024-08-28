-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2024 at 08:58 AM
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
-- Database: `admin_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `id` varchar(50) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `mname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `id`, `fname`, `mname`, `lname`, `email`, `password`, `profile_picture`, `created_at`) VALUES
(1, 'admin/i9icad/23450', 'ggg', 'p', 'masundarau', 'saidahomvungi05@gmail.com', '$2y$10$eXaA1wjqkvd7MMB9lUttq./kUa4gfKipHRqzb9R8iP8Q/kXolujQq', 'image-480x640.jpg', '2024-08-01 11:02:47'),
(3, '69', 'huggpp', 'p', 'jk', 'chriso99123@gmail.com', '$2y$10$Q4gG1qyi56qGoUhpFouyTeWCo9iCxTvQFflM/Mp0SvkXyIUlxV8by', 'image-480x640.jpg', '2024-08-01 11:05:15'),
(6, 'admin/i900icad/23450', 'ui', 'p', 'masundarau', 'chriso00123@gmail.com', '$2y$10$fQBzOEtaZle/0z7g0kf12.KNeORV7wBaatf0le.iJZTDGYBNno8p6', 'image-480x640.jpg', '2024-08-01 11:06:49'),
(7, 'admin/i90ic00ad/23450', 'ggg', 'p', 'masundarau', 'saidah9omvungi05@gmail.com', '$2y$10$npzdhk5TyhYqH/0ZHjxiXePxmnyTD5XL3IiMrCHhghr01gTfxZxXO', 'image-480x640.jpg', '2024-08-01 11:14:42'),
(8, '6', 'huggpp', 'p', 'masundarau', 'chriso910023@gmail.com', '$2y$10$gZRSg9km6Cy3sNDfShMdiOPGYxqtVsNcyh/g2DA4Y.3Um.3NenUqu', 'image-480x640.jpg', '2024-08-01 11:37:12'),
(9, '688', 'huggpp', 'p', 'masundarau', 'chriso9190023@gmail.com', '$2y$10$vI3mx7XZpLlC/gIOVx0uiOEhS2uqy1O4qPZ6l7V3sVEqzsx6E7osG', 'image-480x640.jpg', '2024-08-01 11:39:21'),
(10, '68809', 'huggpp', 'p', 'masundarau', 'chris56@gmail.com', '$2y$10$gbv/4dHkDz4uxNye82h/Peuhl4w.e.YLIAIeXVBS8b/y0HNl1Wy6.', 'image-480x640.jpg', '2024-08-01 11:40:10'),
(11, '878', 'bj', 'k', 'op', 'cgg34@gmail.com', '$2y$10$EDH1q4j6ZcbexHOq73pEfeO9IL1VpDQGNmpoAO2YfX6F5hTPjRy1S', 'image-480x640.jpg', '2024-08-01 11:56:16'),
(12, '8780', 'bjk', 'k', 'op', 'jk34@gmail.com', '$2y$10$Ld6wJYmYDZeyV4InRc6ybenRddmc/u0bqMtU2wsms22ydjI1q6V/m', 'image-480x640.jpg', '2024-08-01 11:56:55');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `feature_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `log_message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`feature_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `feature_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
