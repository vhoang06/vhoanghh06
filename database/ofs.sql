-- --------------------------------------------------------
-- Máy chủ:                      127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Phiên bản:           12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for office_supplies
DROP DATABASE IF EXISTS `office_supplies`;
CREATE DATABASE IF NOT EXISTS `office_supplies` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `office_supplies`;

-- Dumping structure for table office_supplies.brands
DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table office_supplies.brands: ~4 rows (approximately)
DELETE FROM `brands`;
INSERT INTO `brands` (`id`, `name`) VALUES
	(1, 'đồ cũ'),
	(2, 'sunhouse'),
	(3, 'hồng hà'),
	(4, 'limited');

-- Dumping structure for table office_supplies.categories
DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table office_supplies.categories: ~3 rows (approximately)
DELETE FROM `categories`;
INSERT INTO `categories` (`id`, `name`) VALUES
	(1, 'bàn ghế'),
	(2, 'đồ dùng nấu nướng'),
	(3, 'đồ dùng học tập');

-- Dumping structure for table office_supplies.contacts
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table office_supplies.contacts: ~0 rows (approximately)
DELETE FROM `contacts`;

-- Dumping structure for table office_supplies.orders
DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `payment_method` enum('cod','bank') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'cod',
  `shipping_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `shipping_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `shipping_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table office_supplies.orders: ~0 rows (approximately)
DELETE FROM `orders`;
INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `shipping_name`, `shipping_phone`, `shipping_address`, `note`, `created_at`) VALUES
	(1, 1, 1930000.00, 'pending', 'cod', '', '', '', '', '2026-05-13 14:34:45');

-- Dumping structure for table office_supplies.order_items
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table office_supplies.order_items: ~2 rows (approximately)
DELETE FROM `order_items`;
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
	(1, 1, 4, 1, 1850000.00),
	(2, 1, 6, 1, 80000.00);

-- Dumping structure for table office_supplies.products
DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stock` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table office_supplies.products: ~9 rows (approximately)
DELETE FROM `products`;
INSERT INTO `products` (`id`, `name`, `category_id`, `brand_id`, `price`, `description`, `image`, `stock`) VALUES
	(1, 'Bàn làm việc văn phòng gỗ thông', 1, 3, 1850000.00, 'Bàn làm việc bằng gỗ thông tự nhiên cao cấp, bề mặt phủ bóng chống xước, thiết kế tinh tế hiện đại.', NULL, 15),
	(2, 'Chảo Sunhouse', 2, 2, 36180.00, '', 'assets/images/products/prod_69d072d7764c9.jpg', 31),
	(3, 'Bàn học chống cận thông minh', 1, 4, 2500000.00, 'Bàn học có thể điều chỉnh độ cao, mặt bàn nghiêng giúp bé ngồi học đúng tư thế, bảo vệ mắt và cột sống.', NULL, 15),
	(4, 'Ghế xoay văn phòng Ergon', 1, 4, 1850000.00, 'Thiết kế chuẩn Ergonomic, hỗ trợ thắt lưng, lưới thoáng khí, chân xoay linh hoạt.', 'assets/images/products/prod_69d9bb8f49e51.jpg', 20),
	(5, 'Bộ nồi inox Sunhouse 3 đáy', 2, 2, 750000.00, 'Chất liệu inox cao cấp, 3 lớp đáy truyền nhiệt nhanh, giữ nhiệt lâu, dùng được trên mọi loại bếp.', 'assets/images/products/prod_69d072d7764c9.jpg', 30),
	(6, 'Hộp 20 bút bi Thiên Long', 3, 3, 80000.00, 'Bút viết trơn, mực đậm, thiết kế vừa tay cầm, phù hợp cho học sinh và nhân viên văn phòng.', NULL, 100),
	(7, 'Combo 10 quyển vở Hồng Hà', 3, 3, 120000.00, 'Vở kẻ ngang, giấy trắng tự nhiên chống lóa, định lượng 70g/m2, 80 trang.', 'assets/images/products/prod_69d9c1f45bacb.jpg', 50),
	(8, 'Bút ký cao cấp Bizner TL-072', 3, 3, 145000.00, 'Dòng bút ký cao cấp của Thiên Long, nét chữ đều đẹp, vỏ kim loại sang trọng phù hợp ký tài liệu.', NULL, 50),
	(9, 'Sổ tay da cao cấp Hồng Hà Limited', 3, 3, 85000.00, 'Sổ tay bìa da PU cao cấp Hồng Hà, giấy chống lóa, thiết kế lịch lãm phù hợp cho dân văn phòng.', NULL, 40);

-- Dumping structure for table office_supplies.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('user','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'user',
  `email_verified` tinyint(1) DEFAULT '0',
  `verify_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `verify_expires` datetime DEFAULT NULL,
  `reset_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table office_supplies.users: ~3 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `email_verified`, `verify_token`, `verify_expires`, `reset_token`, `reset_expires`, `last_login`, `created_at`) VALUES
	(1, 'admin', 'admin@example.com', '$2y$10$MaMydZahHR0TEP5cch/TAucC50RVw5WLh2Wk868nUT5wBNUwBRofi', 'admin', 1, NULL, NULL, NULL, NULL, NULL, '2026-05-06 14:10:25'),
	(2, 'admin2', 'admin2@example.com', '$2y$10$GQqrRmL641NhM4T5lIaDi.VXmYkPLrbVpImPlG1cc7oUdfc1Owb86', 'admin', 1, NULL, NULL, NULL, NULL, '2026-05-13 14:03:06', '2026-05-13 14:02:48'),
	(3, 'admin123', 'admin123@example.com', '$2y$10$GQqrRmL641NhM4T5lIaDi.VXmYkPLrbVpImPlG1cc7oUdfc1Owb86', 'admin', 1, NULL, NULL, NULL, NULL, '2026-05-21 02:13:12', '2026-05-21 02:13:07');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
