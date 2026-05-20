# 📋 PHẦN 1: TỔNG QUAN VÀ ĐÁNH GIÁ HỆ THỐNG
## DỰ ÁN: WEBSITE THƯƠNG MẠI ĐIỆN TỬ VĂN PHÒNG PHẨM (OFFICE SUPPLIES)

---

## 1. Giới thiệu Dự án
Dự án **Office Supplies** là một ứng dụng web thương mại điện tử chuyên cung cấp các mặt hàng văn phòng phẩm và đồ dùng học tập/nấu nướng cơ bản. Hệ thống được xây dựng trên ngôn ngữ **PHP thuần** và chuyển đổi hoàn toàn sang mô hình **MVC tự xây dựng (Custom MVC Framework)** để tối ưu khả năng bảo trì và nâng cấp.

---

## 2. Đánh giá Mức độ Hoàn thiện cho Production Sơ khai (MVP)

> [!NOTE]
> **Kết luận Đánh giá:** Hệ thống hiện tại **ĐÃ ĐỦ ĐIỀU KIỆN** để đưa vào triển khai ở mức độ **Production sơ khai (Minimum Viable Product - MVP)** phục vụ mục đích vận hành quy mô nhỏ, thử nghiệm lâm sàng hoặc làm **Báo cáo Bài tập lớn xuất sắc**.

### 2.1. Những Điểm Mạnh Đã Hoàn Thành (Sẵn Sàng Cho Triển Khai)

1. **Kiến trúc MVC chuẩn hóa:**
   * Tách biệt hoàn toàn phần lõi hệ thống (`app/Core/`) gồm: `Router`, `Controller`, `Database`, `Model` giúp việc phát triển thêm tính năng mà không ảnh hưởng tới lõi.
   * Tổ chức mã nguồn theo mô hình **Modular** (`app/Modules/`) giúp quản lý từng nhóm chức năng độc lập (Auth, Cart, Order, Product, Admin, Contact).

2. **Nghiệp vụ Thương mại Điện tử Hoàn thiện:**
   * **Khách hàng (Client Front-end):** 
     * Duyệt danh sách sản phẩm trực quan, tìm kiếm nhanh theo tên.
     * Bộ lọc nâng cao theo Danh mục (Categories) và Thương hiệu (Brands).
     * Giỏ hàng linh hoạt (thêm, cập nhật số lượng, xóa sản phẩm).
     * Quy trình đặt hàng COD (Thanh toán khi nhận hàng) và quản lý lịch sử đơn hàng cá nhân.
   * **Quản trị (Admin Back-end):**
     * Dashboard hiển thị thống kê tổng quan doanh thu, số đơn hàng.
     * CRUD đầy đủ và trực quan cho Sản phẩm, Danh mục, Thương hiệu.
     * Xem và thay đổi trạng thái đơn hàng (Pending -> Processing -> Completed / Cancelled).

3. **Cơ chế Bảo mật và An toàn Dữ liệu Vượt trội (Bản nâng cấp v1.1):**
   * **Bảo vệ SQL Injection:** Áp dụng PDO Prepared Statements trên toàn bộ tầng dữ liệu.
   * **CSRF Protection:** Sử dụng token ngẫu nhiên trên toàn bộ biểu mẫu POST ngăn chặn tấn công giả mạo request.
   * **Rate Limiting:** Hạn chế Brute-force đăng nhập bằng cơ chế chặn IP (tối đa 5 lần sai trong 5 phút).
   * **Xác thực Email & Đặt lại mật khẩu:** Cơ chế token bảo mật gửi qua Email có hạn sử dụng (24h/1h).
   * **Bảo mật Cookie Session:** Cấu hình Cookie `httponly` và `samesite=Lax` chặn các cuộc tấn công đánh cắp phiên qua XSS.
   * **Tách biệt Môi trường:** Dùng file cấu hình môi trường `.env` tách biệt DB Credentials ra khỏi mã nguồn nguồn.

---

## 3. Checklist & Lộ trình Triển khai Lên Môi trường Production Thực tế

Để đưa hệ thống lên môi trường chịu tải thực và phục vụ lượng người dùng lớn, cần thực hiện checklist kỹ thuật sau:

| STT | Hạng mục công việc | Yêu cầu Kỹ thuật cụ thể | Trạng thái |
| :--- | :--- | :--- | :---: |
| **1** | **SSL/HTTPS** | Bắt buộc cài đặt chứng chỉ SSL (Let's Encrypt - miễn phí) để mã hóa toàn bộ dữ liệu truyền từ trình duyệt lên máy chủ. | 🔲 Cần làm |
| **2** | **Tắt chế độ Debug hiển thị lỗi** | Sửa đổi `.env` hoặc `config/config.php` thiết lập `ini_set('display_errors', 0)` và `ini_set('log_errors', 1)` để tránh lộ thông tin nhạy cảm của server khi có lỗi phát sinh. | 🔲 Cần làm |
| **3** | **Cấu hình Mailer Thương mại** | Thay vì SMTP Gmail cá nhân (dễ bị khóa/spam), chuyển sang các dịch vụ SMTP Cloud uy tín: **SendGrid**, **Mailgun**, hoặc **Amazon SES**. | 🔲 Cần làm |
| **4** | **Tạo chỉ mục Database (Indexes)** | Thiết lập Indexes trên các cột tìm kiếm thường xuyên: `products.name`, `products.price`, `orders.status`, `users.email` để tăng tốc truy vấn khi dữ liệu lớn. | 🔲 Cần làm |
| **5** | **Sao lưu (Backup) tự động** | Cấu hình script cron job chạy hàng ngày để sao lưu Database tự động lên cloud storage độc lập. | 🔲 Cần làm |
| **6** | **Tối ưu hóa Assets tĩnh** | Nén (Minify) các file CSS, JS và tối ưu hóa dung lượng hình ảnh sản phẩm (chuyển sang định dạng WebP) để tăng tốc độ tải trang. | 🔲 Cần làm |
