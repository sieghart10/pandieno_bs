-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2024 at 08:58 AM
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
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('cash_on_delivery') NOT NULL,
  `order_status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `address_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_orders`
--

INSERT INTO `user_orders` (`order_id`, `book_id`, `user_id`, `price`, `quantity`, `date`, `payment_method`, `order_status`, `address_id`) VALUES
(14, 5, 1, 750.99, 1, '2024-11-17 08:08:14', 'cash_on_delivery', 'pending', 1),
(15, 5, 1, 750.99, 1, '2024-11-17 08:11:47', 'cash_on_delivery', 'pending', 1),
(16, 5, 1, 750.99, 1, '2024-11-17 08:13:58', 'cash_on_delivery', 'pending', 1),
(17, 5, 1, 750.99, 1, '2024-11-17 08:16:08', 'cash_on_delivery', 'pending', 1),
(18, 5, 1, 750.99, 1, '2024-11-17 08:18:43', 'cash_on_delivery', 'pending', 1),
(19, 5, 1, 750.99, 1, '2024-11-17 08:20:58', 'cash_on_delivery', 'pending', 1),
(20, 5, 1, 750.99, 1, '2024-11-17 08:22:56', 'cash_on_delivery', 'pending', 1),
(21, 5, 1, 750.99, 1, '2024-11-17 08:24:06', 'cash_on_delivery', 'pending', 1),
(22, 5, 1, 750.99, 1, '2024-11-17 08:26:48', 'cash_on_delivery', 'pending', 1),
(32, 1, 3, 129.00, 1, '2024-11-20 07:24:05', 'cash_on_delivery', 'pending', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_orders`
--
ALTER TABLE `user_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_orders_ibfk_1` (`user_id`),
  ADD KEY `user_orders_ibfk_2` (`book_id`),
  ADD KEY `fk_order_address_id` (`address_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_orders`
--
ALTER TABLE `user_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_orders`
--
ALTER TABLE `user_orders`
  ADD CONSTRAINT `fk_order_address_id` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`),
  ADD CONSTRAINT `fk_order_book_id` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `fk_order_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
