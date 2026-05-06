-- Dummy Data for Office Supplies
USE `office_supplies`;

-- Thêm sản phẩm mẫu
INSERT INTO `products` (`name`, `category_id`, `brand_id`, `price`, `description`, `image`, `stock`) VALUES
('Bàn học chống cận thông minh', 1, 4, 2500000, 'Bàn học có thể điều chỉnh độ cao, mặt bàn nghiêng giúp bé ngồi học đúng tư thế, bảo vệ mắt và cột sống.', 'assets/images/products/prod_69d06461b03d5.jpg', 15),
('Ghế xoay văn phòng Ergon', 1, 4, 1850000, 'Thiết kế chuẩn Ergonomic, hỗ trợ thắt lưng, lưới thoáng khí, chân xoay linh hoạt.', 'assets/images/products/prod_69d9bb8f49e51.jpg', 20),
('Bộ nồi inox Sunhouse 3 đáy', 2, 2, 750000, 'Chất liệu inox cao cấp, 3 lớp đáy truyền nhiệt nhanh, giữ nhiệt lâu, dùng được trên mọi loại bếp.', 'assets/images/products/prod_69d072d7764c9.jpg', 30),
('Hộp 20 bút bi Thiên Long', 3, 3, 80000, 'Bút viết trơn, mực đậm, thiết kế vừa tay cầm, phù hợp cho học sinh và nhân viên văn phòng.', NULL, 100),
('Combo 10 quyển vở Hồng Hà', 3, 3, 120000, 'Vở kẻ ngang, giấy trắng tự nhiên chống lóa, định lượng 70g/m2, 80 trang.', 'assets/images/products/prod_69d9c1f45bacb.jpg', 50);
