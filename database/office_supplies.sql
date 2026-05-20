-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 14, 2026 lúc 10:59 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `office_supplies`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`) VALUES
(1, 'đồ cũ'),
(2, 'sunhouse'),
(3, 'hồng hà'),
(4, 'limited');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'bàn ghế'),
(2, 'đồ dùng nấu nướng'),
(3, 'đồ dùng học tập');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'vhoanghh', 'viethoangk651@gmail.com', 'hahahahha', '2026-02-07 11:29:56'),
(2, 'vhoanghh', 'viethoangk651@gmail.com', 'hahahahha', '2026-02-07 11:41:30'),
(3, 'vhhh', 'viethoangk651@gmail.com', 'hahahhaha', '2026-02-07 11:45:26'),
(4, 'dvhhh', 'viethoangk651@gmail.com', 'thuong nguoi yeu', '2026-04-04 07:46:00'),
(5, 'dvhhh', 'viethoangk651@gmail.com', 'thuong nguoi yeu', '2026-04-04 07:48:50'),
(6, 'dvhhhh', 'viethoangk651@gmail.com', 'oithoichit', '2026-04-04 08:21:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `payment_method` enum('cod','bank') DEFAULT 'cod',
  `shipping_name` varchar(100) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `shipping_name`, `shipping_phone`, `shipping_address`, `note`, `created_at`) VALUES
(16, 5, 166127.00, 'completed', 'cod', 'viet hoang do', '0369951001', 'bg', 'nhanh', '2026-04-11 10:24:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(20, 16, 11, 1, 118836.00),
(21, 16, 7, 1, 11111.00),
(22, 16, 8, 1, 36180.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `brand_id`, `price`, `description`, `image`, `stock`) VALUES
(7, 'bàn ghế', 1, 1, 11111.00, '', 'assets/images/products/prod_69d06461b03d5.jpg', 18),
(8, 'chảo', 2, 2, 36180.00, '', 'assets/images/products/prod_69d072d7764c9.jpg', 31),
(9, 'bút bi', 3, 3, 3600.00, '', 'assets/images/products/prod_69d0770d679ec.png', 17),
(10, 'bàn học', 1, 4, 36363.00, '', 'assets/images/products/prod_69d9bb6b266bc.jpg', 18),
(11, 'bàn hồng rực rỡ', 1, 4, 118836.00, '', 'assets/images/products/prod_69d9bb8f49e51.jpg', 36),
(12, 'bàn học màu tối', 1, 4, 361833.00, '', 'assets/images/products/prod_69d9bbb9bda1e.jpg', 36),
(13, 'ngòi bút', 3, 3, 1800.00, '', 'assets/images/products/prod_69d9c047b4daa.jpg', 18),
(14, 'thước kẻ khắc tên', 3, 4, 18000.00, '', 'assets/images/products/prod_69d9c0d8a4dcb.jpg', 18),
(15, 'vở ghi', 3, 3, 18000.00, '', 'assets/images/products/prod_69d9c1f45bacb.jpg', 36),
(16, 'dao nấu', 2, 2, 181818.00, '', 'assets/images/products/prod_69d9c8fd45c42.jpg', 18),
(17, 'thớt', 2, 2, 18180.00, '', 'assets/images/products/prod_69d9c91e8e2db.jpg', 18),
(18, 'xoong', 2, 2, 36383.00, '', 'assets/images/products/prod_69d9c93d36e81.jpg', 36);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `email_verified` tinyint(1) DEFAULT 0,
  `verify_token` varchar(64) DEFAULT NULL,
  `verify_expires` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `email_verified`, `created_at`) VALUES
(3, 'dvh06', 'dvhhh@gmail.com', '$2y$10$qq5DcOELhEbsCY2.x07YDOSWlCAGwyiRPuPS6c8CPyQ0VxbTg39Pi', 'user', 1, '2026-04-11 08:39:19'),
(4, 'dvhdvh', 'dvhdvh@gmail.com', '$2y$10$kJi6jUFbi4eTSPfgYQjKLOkVkIIhtYl4UeZR4hRit8z6ZdmuoNJQ6', 'user', 1, '2026-04-11 08:43:13'),
(5, 'vh06', 'vh@gmail.com', '$2y$10$MaMydZahHR0TEP5cch/TAucC50RVw5WLh2Wk868nUT5wBNUwBRofi', 'admin', 1, '2026-04-11 09:04:12'),
(6, '06vh', '06vh@gmail.com', '$2y$10$cAHu7bAZYB8P3MBlkSXXyOZh5Ml2CVvBF9ud.PY1i2J.avvZTGWBy', 'user', 1, '2026-04-11 09:42:42'),
(7, 'admin123', 'admin123@example.com', '$2y$10$GQqrRmL641NhM4T5lIaDi.VXmYkPLrbVpImPlG1cc7oUdfc1Owb86', 'admin', 1, '2026-05-20 12:00:00');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
