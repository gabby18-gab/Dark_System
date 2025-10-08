-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2025 at 05:50 PM
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
-- Database: `fabulous_finds`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `FeedbackID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `SellerID` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `Rating` enum('1','2','3','4','5') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `SellerID` int(11) NOT NULL,
  `OrderDate` datetime NOT NULL,
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `PaymentID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentMethod` varchar(50) NOT NULL,
  `PaymentDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(150) NOT NULL,
  `Category` varchar(100) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `StockQuantity` int(11) NOT NULL,
  `SellerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seller`
--

CREATE TABLE `seller` (
  `SellerID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `ContactInfo` varchar(150) NOT NULL,
  `AssignPrice` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `TransactionID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `PaymentID` int(11) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `TrackingInfo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`FeedbackID`),
  ADD KEY `fk_feedback_user` (`UserID`),
  ADD KEY `fk_feedback_seller` (`SellerID`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`OrderID`,`ProductID`),
  ADD KEY `fk_orderdetails_product` (`ProductID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `fk_order_user` (`UserID`),
  ADD KEY `fk_order_seller` (`SellerID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `fk_payment_order` (`OrderID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `fk_product_seller` (`SellerID`);

--
-- Indexes for table `seller`
--
ALTER TABLE `seller`
  ADD PRIMARY KEY (`SellerID`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `fk_transaction_order` (`OrderID`),
  ADD KEY `fk_transaction_payment` (`PaymentID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `FeedbackID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seller`
--
ALTER TABLE `seller`
  MODIFY `SellerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_seller` FOREIGN KEY (`SellerID`) REFERENCES `seller` (`SellerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_feedback_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `fk_orderdetails_order` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orderdetails_product` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_seller` FOREIGN KEY (`SellerID`) REFERENCES `seller` (`SellerID`),
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_seller` FOREIGN KEY (`SellerID`) REFERENCES `seller` (`SellerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `fk_transaction_order` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaction_payment` FOREIGN KEY (`PaymentID`) REFERENCES `payment` (`PaymentID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
