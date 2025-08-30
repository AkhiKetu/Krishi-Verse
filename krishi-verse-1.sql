-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2025 at 06:25 PM
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
-- Database: `krishi-verse`
--

-- --------------------------------------------------------

--
-- Table structure for table `consumers`
--

CREATE TABLE `consumers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('farmer','supplier','customer') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consumers`
--

INSERT INTO `consumers` (`id`, `name`, `type`, `email`, `phone`, `location`, `status`, `created_at`) VALUES
(1, 'Ador Chakma', 'farmer', 'ador2003@gmail.com', '01894852408', 'Rangamati', 'active', '2025-08-12 14:29:40'),
(2, 'Colince Khisa', 'supplier', 'colincekhisa@gmail.com', '01642725333', 'Bandarban', 'active', '2025-08-12 14:30:40'),
(3, 'Aong Thwai Marma', 'customer', 'aong2005@gmail.com', '01905415670', 'Cumilla', 'active', '2025-08-12 14:31:51'),
(4, 'Dickens Chakma', 'customer', 'dickens2004@gmail.com', '01894852655', 'sylhet', 'inactive', '2025-08-12 14:34:18'),
(5, 'Regan Chakma', 'farmer', 'regan2000@gmail.com', '01905415677', 'Khulna', 'active', '2025-08-12 14:56:40'),
(6, 'Rashid', 'farmer', 'rashid2004@gmail.com', '01556445285', 'Khagrachari', 'inactive', '2025-08-12 15:10:42'),
(7, 'Joy Chakma', 'farmer', 'joy2001@gmail.com', '01905415620', 'Cox\'s Bazar', 'inactive', '2025-08-12 16:19:15');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `doc_type` varchar(100) NOT NULL,
  `doc_number` varchar(100) NOT NULL,
  `shipment_id` varchar(100) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `distribution_center` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `status` enum('Pending Review','Approved','Rejected','Reviewed') DEFAULT 'Pending Review',
  `compliance` enum('Yes','No') DEFAULT 'No',
  `file_size` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `doc_type`, `doc_number`, `shipment_id`, `product_name`, `distribution_center`, `issue_date`, `expiry_date`, `status`, `compliance`, `file_size`, `file_path`, `created_at`, `updated_at`) VALUES
(1, 'Bill', '234', '232', 'Mango', 'Rangamati', '2025-08-12', '2025-08-29', 'Reviewed', 'Yes', '2216.2', 'uploads/doc_689b75c406dc16.43375568.pdf', '2025-08-12 17:11:32', '2025-08-12 17:22:19'),
(2, 'Invoice', '2221', '2003', 'Banana', 'Cumilla', '2025-08-12', '2025-08-30', 'Reviewed', 'Yes', '79.14', 'uploads/doc_689b763095ad20.14486162.pdf', '2025-08-12 17:13:20', '2025-08-12 17:22:16'),
(3, 'Bill', '2222', '2004', 'Cucumber', 'Dinajpur', '2025-08-13', '2025-08-25', 'Pending Review', 'No', '3.96', '', '2025-08-12 17:24:24', '2025-08-12 17:24:24');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `main_storage` float NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `in_warehouse` float NOT NULL,
  `last_available` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `main_storage`, `warehouse_id`, `in_warehouse`, `last_available`) VALUES
(1, 'p-1', 500, 1, 200, 300),
(2, 'p-11', 260, 1, 155, 105),
(3, 'p-2', 345, 2, 240.78, 104.22),
(4, 'p-3', 300, 1, 25, 275);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `productID` varchar(50) NOT NULL,
  `productName` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `packaging` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('in-stock','stock-out') NOT NULL,
  `plantingDate` date DEFAULT NULL,
  `harvestDate` date DEFAULT NULL,
  `shelfLife` varchar(100) DEFAULT NULL,
  `storageTemp` varchar(100) DEFAULT NULL,
  `humidity` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`productID`, `productName`, `category`, `price`, `packaging`, `location`, `status`, `plantingDate`, `harvestDate`, `shelfLife`, `storageTemp`, `humidity`) VALUES
('p-1', 'Potato', 'Vegetables', 30.00, '5 kg crates', 'Rangamati', 'in-stock', '2025-07-06', '2025-08-09', '10 Days', 'Room Temp', '70%'),
('p-11', 'Pineapple', 'Fruits', 45.00, '15 kg crates', 'Rangamati', 'in-stock', '2025-06-01', '2025-08-30', '10 Days', '27 Degree', '95%'),
('p-2', 'Mango', 'Fruits', 40.00, '7 kg crates', 'Rangamati', 'in-stock', '2025-07-10', '2025-08-10', '20 Days', '30 Degree', '40%'),
('p-3', 'Orange', 'Fruits', 220.00, '15 kg crates', 'Rangamati', 'in-stock', '2025-07-10', '2025-08-24', '5 Days', 'Room Temp', '30%'),
('p-4', 'Bamboo-Shoot', 'Vegetables', 90.00, '50 Kg Crates', 'Rangamati', 'in-stock', '2025-07-15', '2025-08-15', '2 Days', 'Room Temp', '45%'),
('p-5', 'Sunflower Seeds', 'Seeds', 245.00, '6 Kg Crates', 'Cumilla', 'in-stock', '2025-06-02', '2025-08-05', '40 Days', 'Room Temp', '95%'),
('p-6', 'Radish', 'Vegetables', 80.00, '20 Kg', 'Bandarban', 'stock-out', '2025-01-10', '2025-02-10', '15 Days', '30 Degree', '76%'),
('p-7', 'Cabbage', 'Vegetables', 40.00, '45 Kg', 'Rangamati', 'stock-out', '2025-01-01', '2025-02-01', '5 Days', '27 Degree', '65%'),
('p-8', 'Banana', 'Fruits', 80.00, '60 Kg Crates', 'Khagrachari', 'in-stock', '2025-07-01', '2025-08-20', '7 Days', 'Room Temp', '90%'),
('p-9', 'Sweet-potato', 'Vegetables', 100.00, '40 Kg Crates', 'Rangamati', 'in-stock', '2025-06-30', '2025-09-20', '30 Days', 'Room Temp', '95%');

-- --------------------------------------------------------

--
-- Table structure for table `transportation`
--

CREATE TABLE `transportation` (
  `id` int(11) NOT NULL,
  `origin` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `product` varchar(255) NOT NULL,
  `weight` varchar(100) NOT NULL,
  `distance` int(11) NOT NULL,
  `temperature` varchar(50) NOT NULL,
  `humidity` varchar(50) NOT NULL,
  `vehicle` varchar(100) NOT NULL,
  `driver` varchar(100) NOT NULL,
  `schedule_date` date NOT NULL,
  `schedule_time` time NOT NULL,
  `priority` enum('High','Low') NOT NULL,
  `status` varchar(50) DEFAULT 'Scheduled',
  `costing` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transportation`
--

INSERT INTO `transportation` (`id`, `origin`, `destination`, `product`, `weight`, `distance`, `temperature`, `humidity`, `vehicle`, `driver`, `schedule_date`, `schedule_time`, `priority`, `status`, `costing`, `created_at`, `updated_at`) VALUES
(1, 'Warehouse-A', 'Fresh Market Distribution', 'Bamboo-Shoot', '2400 Kg', 234, '27 Degree', '70%', 'RT-001', 'John Smith', '2025-08-13', '14:00:00', 'High', 'Scheduled', 3510.00, '2025-08-12 18:07:19', '2025-08-12 18:07:19'),
(3, 'Warehouse-A', 'Retail Chain B', 'Bamboo-Shoot', '200 Kg', 120, 'Room Temp', '95%', 'RT-002', 'Priya Das', '2025-08-15', '16:00:00', 'Low', 'Scheduled', 1800.00, '2025-08-12 18:11:05', '2025-08-12 18:11:05'),
(4, 'Warehouse-C', 'Processing Plant A', 'Cabbage', '150 Kg', 200, '25 Degree', '70%', 'DC-003', 'Amit Rahman', '2025-08-25', '07:00:00', 'High', 'Scheduled', 3000.00, '2025-08-12 18:17:22', '2025-08-12 18:17:22'),
(5, 'Warehouse-A', 'Processing Plant A', 'Mango', '250 Kg', 3000, '30 Degree', '75%', 'DC-004', 'Mehedi Hasan', '2025-08-20', '12:00:00', 'High', 'Scheduled', 45000.00, '2025-08-12 18:27:47', '2025-08-12 18:27:47'),
(6, 'Warehouse-B', 'Fresh Market Distribution', 'Sunflower Seeds', '300 Kg', 200, '20 Degree', '85%', 'RT-001', 'Mehedi Hasan', '2025-08-30', '01:15:00', 'Low', 'Scheduled', 3000.00, '2025-08-16 07:15:14', '2025-08-16 07:15:14'),
(7, 'Warehouse-F', 'Fresh Market Distribution', 'Banana', '400 Kg', 1300, 'Room Temp', '75%', 'DC-003', 'Amit Rahman', '2025-08-25', '06:00:00', 'High', 'Scheduled', 19500.00, '2025-08-16 07:17:09', '2025-08-16 07:17:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `designation` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `username`, `designation`, `password`, `created_at`) VALUES
(6, 'Akhi Ketu Chakma', 'akhiketu2003@gmail.com', 'Akhiketu', 'warehouseManager', '$2y$10$IYnJH22suyFss5IOjMO8mO.48guhjCFIrjyTIt/LFBLuGarWvkJA.', '2025-08-05 09:18:30'),
(7, 'Preshati Shreya Dewan', 'preshati2002@gmail.com', 'Preshati', 'supplier', '$2y$10$5K.Ny0mhE9FVW3obx0Rds.HW9RQy95agENaSAuxmC8JixfA4WKzri', '2025-08-05 09:26:52'),
(8, 'Dickens  Chakma', 'dickens2004@gmail.com', 'Dickens', 'warehouseManager', '$2y$10$pm7A.potKkxCIrVy6GLKz.DhygosL.rt5ftHaS04CFvX6XYwEDIju', '2025-08-05 14:03:21'),
(9, 'Aong Thwai Wong Marma', 'aong2005@gmail.com', 'Aong', 'supplier', '$2y$10$l7115EwYtyS/xaSyivz12OwxniP2bRQTiEkZCvnthBUWzEdIPzSkW', '2025-08-05 14:41:44'),
(10, 'Jenin', 'jenin2004@gmail.com', 'Jenin', 'warehouseManager', '$2y$10$YSUt/1BrMh1RDR.7dh3AzubwPqmuY4rLUFnCd8Yywf7xV2kMrYqJW', '2025-08-05 16:57:31'),
(11, 'Mansura', 'mansura2001@gmail.com', 'Mansura', 'warehouseManager', '$2y$10$DKidUDNV/pZUe2A4NsQIKOVab.fbx2eg4KmwTI2771mQ.gQXcbp7.', '2025-08-07 17:39:44'),
(12, 'Regan Chakma', 'regan2000@gmail.com', 'Regan', 'supplier', '$2y$10$pFG4YWQ8vEnT3IuYX4sDKeD2KqGTbr0saJGVzUX6bd8vSwaFV02.i', '2025-08-07 17:46:06'),
(13, 'Devrup Chakma', 'devrup2005@gmail.com', 'Devrup', 'supplier', '$2y$10$09PmBgkmNyEWec6sIpUkYuXKWaUeUxtcoUh3bKSXva9eKZ4t5GT8S', '2025-08-09 10:43:12'),
(14, 'Kalvin Chakma', 'kalvin2001@gmail.com', 'Kalvin', 'warehouseManager', '$2y$10$rDJXo2QeqspGsF5lU1tEIOe6MweUh8zdpRA1ECn6TO3IV33BUTqCa', '2025-08-10 08:22:22'),
(15, 'rose', 'rehnuma23@gmail.com', 'rose', 'warehouseManager', '$2y$10$gzmEGCFj5WU1FKoOgDORE.H5fD/0WmJ18YMMC2RnkFg/VXC7Xd0fK', '2025-08-11 07:26:07'),
(16, 'rimi', 'rimi@gmail.com', 'rimi', 'supplier', '$2y$10$h8IL3VydXMJvbcCiF/Jn7u/F0Yh1mg2bF0egrioBL5CURsFXdnGx2', '2025-08-11 07:27:38');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `capacity` int(11) NOT NULL,
  `manager` varchar(100) NOT NULL,
  `type` enum('Cold Storage','Dry Storage') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `location`, `capacity`, `manager`, `type`) VALUES
(1, 'Warehouse-A', 'Rangamati', 300, 'Preshati', 'Cold Storage'),
(2, 'Warehouse-B', 'Cumilla', 400, 'Mansura', 'Dry Storage'),
(3, 'Warehouse-C', 'Bandarban', 600, 'Aong', 'Cold Storage'),
(4, 'Warehouse-D', 'Sylhet', 200, 'Aneet', 'Dry Storage'),
(5, 'Warehouse-E', 'Dinajpur', 500, 'Shayan', 'Cold Storage'),
(6, 'Warehouse-F', 'Khagrachari', 400, 'Rehnuma', 'Cold Storage');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `consumers`
--
ALTER TABLE `consumers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `warehouse_id` (`warehouse_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`productID`);

--
-- Indexes for table `transportation`
--
ALTER TABLE `transportation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consumers`
--
ALTER TABLE `consumers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transportation`
--
ALTER TABLE `transportation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`productID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table structure for table `packaging`
--

CREATE TABLE `packaging` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batch_id` varchar(50) NOT NULL,
  `packaging_type` varchar(100) NOT NULL,
  `expire_date` date NOT NULL,
  `storage_temp` varchar(50) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `packaging_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`batch_id`) REFERENCES `products` (`productID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
