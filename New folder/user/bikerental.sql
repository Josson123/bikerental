-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2024 at 10:47 AM
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
-- Database: `bikerental`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin` varchar(50) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin`, `password`) VALUES
('admin', 'admin@123');

-- --------------------------------------------------------

--
-- Table structure for table `bike`
--

CREATE TABLE `bike` (
  `sl_no` bigint(255) NOT NULL,
  `bike_no` int(3) UNSIGNED NOT NULL COMMENT 'this shows bike number',
  `bike_name` varchar(30) NOT NULL COMMENT 'Enters bike name by admin',
  `bike_class` varchar(50) NOT NULL COMMENT 'bike class entered by admin',
  `booking_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1:booked,0:available',
  `bike_img` varchar(255) NOT NULL COMMENT 'Bike image by admin',
  `brand` varchar(20) NOT NULL,
  `price` float(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bike`
--

INSERT INTO `bike` (`sl_no`, `bike_no`, `bike_name`, `bike_class`, `booking_status`, `bike_img`, `brand`, `price`) VALUES
(2, 100, 'splendor', 'standard', 1, 'images\\local\\splendor.jpeg', 'Hero Honda', 30.00),
(0, 2121, 'ola s1 pro', 'premium', 0, '../images/local/ola S1 pro (1).jpg', 'ola', 43.00),
(0, 3010, 'Bullet 350', 'premium', 0, '../images/localBullet 350.jpg', 'Royal Enfield', 50.00),
(1, 4656, 'access', 'standard', 0, 'images\\local\\access.jpg', 'suzuki', 20.00),
(0, 6060, 'Himalayan', 'mountain', 1, '../images/localHimalayan.jpg', 'Royal Enfield', 45.00);

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_no` int(5) UNSIGNED NOT NULL,
  `pickup_date` datetime NOT NULL,
  `dropoff_date` datetime NOT NULL,
  `user_name` varchar(50) NOT NULL COMMENT 'user name from user table\r\n\r\n',
  `bike_no` int(3) UNSIGNED NOT NULL,
  `booking_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_no`, `pickup_date`, `dropoff_date`, `user_name`, `bike_no`, `booking_date`) VALUES
(6, '2024-10-22 00:00:00', '2024-10-23 00:00:00', 'ammavan', 100, '2024-10-22'),
(7, '2024-10-23 00:00:00', '2024-10-23 00:00:00', 'ammavan', 3010, '2024-10-22'),
(8, '2024-10-23 00:00:00', '2024-10-24 00:00:00', 'ammavan', 6060, '2024-10-22'),
(9, '2024-10-24 00:00:00', '2024-10-25 00:00:00', 'josson', 200, '2024-10-24');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_name` varchar(50) NOT NULL COMMENT 'user name entered',
  `email` varchar(80) NOT NULL COMMENT 'Email entered ',
  `phone_no` varchar(10) DEFAULT NULL COMMENT 'Phone number entered (Not necessary)',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='This is the table containing user info';

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_name`, `email`, `phone_no`, `password`) VALUES
('ammavan', 'ammavan@gmail.com', '8574123159', '$2y$10$nac8FwWM4x3M22xg8UqN9u.EA1dxXT2I3bV4AnPOFnLUrBKiS/JL6'),
('jagan', 'jagan@gmail.com', '', '$2y$10$RE0fJVz7QH4GFb9BBnh15euumnBSxszzmAgRKB27W1T6sDuAgd7oe'),
('joss', 'joss@gmail.com', '8585471230', '$2y$10$x59iUgzpYPkRkiYSD/Yb2e5OlrJY/kToKkqmatrYrPuYKDvyeH.8y'),
('josson', 'joseph@gmail.com', '7894561230', '$2y$10$yC5hIZZb0QC6NoeeVmROouymF5qcLw2tS1BNsasQNlJfV1icc4REC');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bike`
--
ALTER TABLE `bike`
  ADD PRIMARY KEY (`bike_no`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_no`),
  ADD KEY `test` (`bike_no`),
  ADD KEY `dest` (`user_name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_no` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `dest` FOREIGN KEY (`user_name`) REFERENCES `user` (`user_name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
