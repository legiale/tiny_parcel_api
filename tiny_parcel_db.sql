-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.11-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table tiny_parcel.authentication_tokens
DROP TABLE IF EXISTS `authentication_tokens`;
CREATE TABLE IF NOT EXISTS `authentication_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expired_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Dumping data for table tiny_parcel.authentication_tokens: ~1 rows (approximately)
DELETE FROM `authentication_tokens`;
/*!40000 ALTER TABLE `authentication_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `authentication_tokens` ENABLE KEYS */;

-- Dumping structure for table tiny_parcel.customers
DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `last_login` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`customer_id`) USING BTREE,
  KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table tiny_parcel.customers: ~1 rows (approximately)
DELETE FROM `customers`;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` (`customer_id`, `username`, `password`, `customer_name`, `last_login`) VALUES
	(1, 'legiale', '92432d33bbf7ef4fcba9a47979811ca7', 'Admin', '2022-01-26 21:17:58'),
	(2, 'test1', '5a105e8b9d40e1329780d62ea2265d8a', 'Test', '2022-01-27 04:29:56');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;

-- Dumping structure for table tiny_parcel.parcels
DROP TABLE IF EXISTS `parcels`;
CREATE TABLE IF NOT EXISTS `parcels` (
  `parcel_id` int(11) NOT NULL AUTO_INCREMENT,
  `parcel_customer_id` int(11) NOT NULL,
  `parcel_name` varchar(255) NOT NULL,
  `parcel_weight` float NOT NULL DEFAULT 0,
  `parcel_volume` float NOT NULL DEFAULT 0,
  `parcel_declared_value` float NOT NULL DEFAULT 0,
  `parcel_chosen_model` enum('BY_VALUE','BY_WEIGHT','BY_VOLUME') NOT NULL,
  `parcel_quote` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`parcel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table tiny_parcel.parcels: ~2 rows (approximately)
DELETE FROM `parcels`;
/*!40000 ALTER TABLE `parcels` DISABLE KEYS */;
/*!40000 ALTER TABLE `parcels` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
