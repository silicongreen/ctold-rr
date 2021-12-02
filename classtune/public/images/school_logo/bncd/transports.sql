-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 14, 2021 at 09:57 AM
-- Server version: 5.7.34-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `classtune`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_vehicles`
--

DROP TABLE IF EXISTS `attendance_vehicles`;
CREATE TABLE `attendance_vehicles` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `note` text,
  `attendance_date` date NOT NULL,
  `is_absent` tinyint(4) NOT NULL DEFAULT '0',
  `pickup_or_drop` tinyint(4) NOT NULL DEFAULT '1',
  `school_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
CREATE TABLE `routes` (
  `id` int(11) NOT NULL,
  `destination` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cost` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `main_route_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `route_schedules`
--

DROP TABLE IF EXISTS `route_schedules`;
CREATE TABLE `route_schedules` (
  `id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `weekday_id` int(11) NOT NULL,
  `home_pickup_time` time DEFAULT NULL,
  `school_pickup_time` time DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transports`
--

DROP TABLE IF EXISTS `transports`;
CREATE TABLE `transports` (
  `id` int(11) NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `route_id` int(11) DEFAULT NULL,
  `bus_fare` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `receiver_type` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transport_fees`
--

DROP TABLE IF EXISTS `transport_fees`;
CREATE TABLE `transport_fees` (
  `id` int(11) NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `bus_fare` decimal(8,4) DEFAULT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `transport_fee_collection_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `receiver_type` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transport_fee_collections`
--

DROP TABLE IF EXISTS `transport_fee_collections`;
CREATE TABLE `transport_fee_collections` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `school_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `fee_collection_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_no` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `main_route_id` int(11) DEFAULT NULL,
  `no_of_seats` int(11) DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `driver` varchar(255) DEFAULT NULL,
  `bus_mother` int(11) DEFAULT '0',
  `support_staff` int(11) DEFAULT '0',
  `bus_mother_contact` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_vehicles`
--
ALTER TABLE `attendance_vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`,`vehicle_id`,`attendance_date`,`is_absent`,`pickup_or_drop`,`school_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_routes_on_school_id` (`school_id`),
  ADD KEY `main_route_id` (`main_route_id`);

--
-- Indexes for table `route_schedules`
--
ALTER TABLE `route_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `route_id` (`route_id`,`weekday_id`,`school_id`);

--
-- Indexes for table `transports`
--
ALTER TABLE `transports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_transports_on_school_id` (`school_id`),
  ADD KEY `receiver_id` (`receiver_id`,`vehicle_id`,`route_id`);

--
-- Indexes for table `transport_fees`
--
ALTER TABLE `transport_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_transport_fees_on_school_id` (`school_id`),
  ADD KEY `indices_on_transactions` (`receiver_id`,`transaction_id`),
  ADD KEY `transport_fee_collection_id` (`transport_fee_collection_id`);

--
-- Indexes for table `transport_fee_collections`
--
ALTER TABLE `transport_fee_collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_transport_fee_collections_on_school_id` (`school_id`),
  ADD KEY `index_transport_fee_collections_on_batch_id` (`batch_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_vehicles_on_school_id` (`school_id`),
  ADD KEY `main_route_id` (`main_route_id`,`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_vehicles`
--
ALTER TABLE `attendance_vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;
--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;
--
-- AUTO_INCREMENT for table `route_schedules`
--
ALTER TABLE `route_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=274;
--
-- AUTO_INCREMENT for table `transports`
--
ALTER TABLE `transports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1240;
--
-- AUTO_INCREMENT for table `transport_fees`
--
ALTER TABLE `transport_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `transport_fee_collections`
--
ALTER TABLE `transport_fee_collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;
--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
