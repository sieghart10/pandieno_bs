-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2024 at 08:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pandieno_bookstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_orders`
--

CREATE TABLE `user_orders` (
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `payment_method` enum('cash_on_delivery') NOT NULL,
  `order_status` enum('pending','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_orders`
--
ALTER TABLE `user_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_orders_ibfk_1` (`user_id`),
  ADD KEY `user_orders_ibfk_2` (`book_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_orders`
--
ALTER TABLE `user_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
