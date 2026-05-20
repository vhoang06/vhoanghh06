# 📋 THÔNG TIN DỰ ÁN ĐỂ VẼ UML & VIẾT BÁO CÁO WORD

> 📌 **Hướng dẫn:** Điền thông tin thực tế từ code của bạn vào các mục `[...]`. Tôi sẽ dùng dữ liệu này để vẽ UML chính xác và viết lại báo cáo Word chuẩn kỹ thuật.

---

## 1. 🛠️ TECH STACK & KIẾN TRÚC
- **Frontend:** HTML5, CSS Bootstrap 5, Font Awesome 6 (tích hợp trực tiếp trong các tệp View và Layout của PHP)
- **Backend:** PHP thuần (PSR-4 autoloading qua Composer và manual fallback spl_autoload_register) xây dựng trên mô hình Custom MVC Framework
- **Database & ORM:** MySQL / MariaDB (truy xuất dữ liệu an toàn qua PDO với Prepared Statements)
- **Auth & Security:** 
  - Đăng ký & Đăng nhập phân quyền (`user` và `admin`).
  - CSRF Protection (Mã hóa Token trên mọi POST request: login, register, cart, checkout, admin CRUD).
  - Rate Limiting (Giới hạn tối đa 5 lần thử login sai trong 5 phút per IP để chống Brute Force).
  - Email Verification (Gửi mail xác thực qua token bảo mật thời hạn 24h).
  - Password Reset (Gửi mail đặt lại mật khẩu với token bảo mật thời hạn 1h).
  - Session Security (Cấu hình Cookie an toàn với thuộc tính `HttpOnly` và `SameSite=Lax`).
  - Input Sanitization & Validation (Sử dụng các helper filter, sanitize dữ liệu đầu vào chống XSS và kiểm tra định dạng email, mật khẩu).
- **State Management:** PHP Session (Lưu trữ trạng thái đăng nhập, phân quyền và dữ liệu giỏ hàng tạm thời)
- **Deployment/DevOps:** Môi trường phát triển cục bộ (XAMPP / Laragon / PHP built-in server). Hỗ trợ tách biệt thông số cấu hình nhạy cảm qua tệp môi trường `.env`.
- **Kiến trúc tổng quan:** Kiến trúc Client-Server dựa trên mô hình MVC tự dựng (Custom MVC Framework) với cơ chế Single Entry Point (`public/index.php`) điều hướng yêu cầu qua bộ định tuyến `App\Core\Router`.

---

## 2. 👥 PHÂN QUYỀN & VAI TRÒ (ROLES)
- `Guest / Khách vãng lai:` Duyệt xem danh sách sản phẩm, tìm kiếm sản phẩm, lọc sản phẩm theo thương hiệu/danh mục, xem chi tiết sản phẩm, quản lý giỏ hàng tạm thời (Session), gửi biểu mẫu liên hệ, đăng ký tài khoản mới (xác thực qua email), đăng nhập, sử dụng tính năng quên/đặt lại mật khẩu.
- `Customer / Khách hàng:` Đăng nhập vào hệ thống, kế thừa toàn bộ quyền của Khách vãng lai, quản lý giỏ hàng, đặt hàng thanh toán COD, xem danh sách đơn hàng đã mua và theo dõi trạng thái đơn hàng của bản thân, thực hiện đăng xuất.
- `Admin / Quản trị viên:` Đăng nhập vào phân hệ quản trị, truy cập Dashboard thống kê (tổng doanh thu của các đơn completed, tổng số đơn hàng, sản phẩm và người dùng), thực hiện quản lý CRUD Sản phẩm (Thêm, Sửa, Xóa, tải lên ảnh sản phẩm), quản lý CRUD Đơn hàng (Xem danh sách, Tạo đơn hàng mới từ danh sách khách hàng và sản phẩm, Sửa thông tin giao nhận, Cập nhật trạng thái duyệt đơn hàng), quản lý Người dùng (Xem danh sách tài khoản trong hệ thống).

---

## 3. 🎯 CHỨC NĂNG CHI TIẾT (USE CASES)
### 🔹 Customer
- [x] Xem danh sách sản phẩm + Lọc/Tìm kiếm (theo: tên sản phẩm, danh mục sản phẩm, thương hiệu)
- [x] Xem chi tiết sản phẩm + Ảnh và mô tả chi tiết
- [x] Đăng ký / Đăng nhập (Rate limited) / Quên mật khẩu & Đặt lại mật khẩu
- [x] Giỏ hàng (Thêm/Sửa số lượng/Xóa - đồng bộ lưu trữ trong PHP Session)
- [x] Thanh toán (Phương thức: COD - Nhận hàng thanh toán hoặc Chuyển khoản ngân hàng)
- [x] Theo dõi đơn hàng đã mua + Xem chi tiết trạng thái đơn hàng
- [ ] Đánh giá/Bình luận sản phẩm *(Chưa được code trong hệ thống)*
- [ ] Sử dụng Voucher/Khuyến mãi *(Chưa được code trong hệ thống)*

### 🔹 Admin
- [x] Dashboard thống kê (Doanh thu thực tế đơn completed, Tổng số đơn hàng, Sản phẩm, Khách hàng)
- [x] Quản lý Sản phẩm (CRUD + Upload ảnh sản phẩm lên thư mục server)
- [x] Quản lý Đơn hàng (Tạo đơn hàng mới, Xem chi tiết, Sửa thông tin giao hàng, Hủy/Cập nhật trạng thái duyệt đơn)
- [x] Quản lý Người dùng & Nhân viên (Xem danh sách tài khoản người dùng trong hệ thống)
- [ ] Quản lý Khuyến mãi & Voucher *(Chưa phát triển)*
- [ ] Quản lý Danh mục & Thuộc tính sản phẩm *(Thực hiện trực tiếp trong Database hoặc sử dụng tệp public admin cũ)*
- [ ] Xuất báo cáo (Excel/PDF) *(Chưa phát triển)*
- [ ] Cấu hình hệ thống (Banner, Shipping fee) *(Chưa phát triển)*

---

## 4. 🗄️ CẤU TRÚC CƠ SỞ DỮ LIỆU (TABLES & RELATIONSHIPS)

| Tên Bảng | Khóa Chính | Các trường quan trọng | Quan hệ |
|---|---|---|---|
| `users` | `id` | `username, email, password, role, email_verified, verify_token, verify_expires, reset_token, reset_expires, last_login, created_at` | 1-N với `orders` |
| `categories` | `id` | `name` | 1-N với `products` |
| `brands` | `id` | `name` | 1-N với `products` |
| `products` | `id` | `name, category_id, brand_id, price, description, image, stock` | N-1 với `categories`, `brands`, 1-N với `order_items` |
| `orders` | `id` | `user_id, total_amount, status, payment_method, shipping_name, shipping_phone, shipping_address, note, created_at` | 1-N với `order_items`, N-1 với `users` |
| `order_items` | `id` | `order_id, product_id, quantity, price` | FK đến `orders` (ON DELETE CASCADE), FK đến `products` |
| `contacts` | `id` | `name, email, message, created_at` | Bảng độc lập ghi nhận ý kiến phản hồi |

---

## 5. 🔄 LUỒNG NGHIỆP VỤ CHÍNH (WORKFLOWS)
### 🔸 Luồng Đặt hàng & Thanh toán (COD)
1. Khách hàng đăng nhập, thêm sản phẩm mong muốn vào giỏ hàng (dữ liệu giỏ lưu trong Session).
2. Khách hàng truy cập trang thanh toán (route: `checkout`), hệ thống kiểm tra trạng thái đăng nhập.
3. Khách hàng nhập thông tin giao nhận (Tên người nhận, số điện thoại, địa chỉ giao hàng) và chọn phương thức thanh toán (mặc định COD).
4. Hệ thống tiến hành xác thực dữ liệu đầu vào. Tiếp theo, hệ thống mở một **Database Transaction**, thực hiện khoá hàng tồn kho của sản phẩm (`SELECT stock FOR UPDATE`) để kiểm tra số lượng tồn kho thực tế.
5. Nếu đủ số lượng, hệ thống tạo bản ghi mới trong bảng `orders` và các bản ghi chi tiết trong bảng `order_items`, đồng thời tự động cập nhật giảm số lượng tồn kho tương ứng của sản phẩm trong bảng `products`.
6. Hệ thống cam kết transaction (`COMMIT`), làm sạch dữ liệu giỏ hàng Session và chuyển hướng khách hàng về trang lịch sử đơn hàng kèm theo thông báo thành công.

### 🔸 Luồng Duyệt đơn (Admin)
1. Quản trị viên đăng nhập vào hệ thống phân hệ admin (route: `admin/orders`).
2. Xem danh sách các đơn hàng hiện có hoặc xem chi tiết một đơn hàng cụ thể (route: `admin/orders/detail`).
3. Chọn trạng thái mới để cập nhật cho đơn hàng (Ví dụ: từ `pending` sang `processing`, `completed` hoặc `cancelled`).
4. Gửi form cập nhật trạng thái đơn hàng (route: `admin/orders/update`). Hệ thống thực thi prepared statement cập nhật trường `status` của đơn hàng trong bảng `orders` và chuyển hướng kèm thông báo thành công.

### 🔸 Luồng Đăng nhập & Xác thực (Rate Limiting & Email Verification)
1. Người dùng gửi thông tin đăng nhập (username/email và password) qua form POST của route `login`.
2. Hệ thống kiểm tra tần suất đăng nhập từ địa chỉ IP hiện tại. Nếu vượt quá 5 lần đăng nhập sai trong vòng 5 phút, hệ thống từ chối xử lý và hiển thị thông báo lỗi yêu cầu đợi thêm.
3. Nếu chưa vượt giới hạn, hệ thống truy vấn thông tin người dùng từ bảng `users` bằng prepared statement.
4. Hệ thống kiểm tra mật khẩu bằng `password_verify()`. Nếu chính xác, tiếp tục kiểm tra xem trường `email_verified` có bằng 1 hay không.
5. Nếu email đã xác thực, hệ thống thiết lập các thông tin người dùng vào `$_SESSION`, cập nhật trường `last_login = NOW()`, reset bộ đếm rate limit và chuyển hướng người dùng về trang chủ (hoặc trang trước đó).

---

## 6. 🌐 API & TÍCH HỢP (NẾU CÓ BACKEND)
- **Base URL:** `index.php?route=` (Điều tuyến thông qua Query String Parameter)
- **Định dạng:** `MVC Action Controller rendering HTML / Form-data POST`
- **Auth Header:** `Cookie-based Session Authentication (PHPSESSID)`
- **5 Endpoint tiêu biểu:**
  | Method | Route | Mô tả | Auth? |
  |---|---|---|---|
  | `POST` | `login` | Xử lý đăng nhập, lưu session & update last_login | ❌ (Cần CSRF) |
  | `GET` | `products` | Danh sách sản phẩm, hỗ trợ lọc theo category/brand | ❌ |
  | `POST` | `add_to_cart` | Thêm sản phẩm vào giỏ hàng Session | ❌ (Cần CSRF) |
  | `POST` | `checkout` | Tạo đơn hàng mới từ giỏ hàng Session, trừ tồn kho | ✅ (Customer) |
  | `POST` | `admin/orders/update` | Cập nhật trạng thái của đơn hàng | ✅ (Admin) |

---

## 7. ⚖️ BUSINESS RULES & RÀNG BUỘC
- **Mật khẩu người dùng:** Phải có độ dài tối thiểu 6 ký tự, bắt buộc chứa ít nhất 1 chữ cái (a-z, A-Z) và ít nhất 1 chữ số (0-9) để đảm bảo độ an toàn.
- **Tên đăng nhập & Email:** Phải là duy nhất (`UNIQUE`) trong cơ sở dữ liệu bảng `users`, không cho phép trùng lặp khi đăng ký mới.
- **Quản lý kho hàng:** Kiểm tra số lượng tồn kho trước khi tạo đơn và thực hiện giảm tồn kho tự động trong Database Transaction. Không cho phép đặt hàng vượt quá số lượng tồn kho khả dụng.
- **Rate limiting:** Hạn chế brute-force đăng nhập ở mức tối đa 5 lần thử sai trong vòng 5 phút (300 giây) cho mỗi địa chỉ IP.
- **Hạn dùng token bảo mật:** Token xác thực đăng ký tài khoản có hiệu lực trong vòng 24 giờ. Token đặt lại mật khẩu có hiệu lực trong vòng 1 giờ từ khi tạo.

---

## 8. 🖼️ CẤU TRÚC FRONTEND & ROUTING
- **Public Pages:** 
  - Trang chủ: `home`
  - Đăng nhập: `login`
  - Đăng ký: `register`
  - Quên mật khẩu: `forgot_password`
  - Đặt lại mật khẩu: `reset_password`
  - Xác thực email: `verify_email`
  - Danh sách sản phẩm: `products`
  - Chi tiết sản phẩm: `product_detail`
  - Xem theo thương hiệu: `brands`
  - Giỏ hàng: `cart`
  - Trang liên hệ: `contact`
- **Protected (Customer):**
  - Danh sách đơn hàng cá nhân: `orders`
  - Trang đặt hàng/thanh toán: `checkout`
- **Protected (Admin):**
  - Trang Dashboard Admin: `admin`
  - Quản lý sản phẩm: `admin/products`
  - Thêm sản phẩm: `admin/products/add`
  - Sửa sản phẩm: `admin/products/edit`
  - Xóa sản phẩm: `admin/products/delete`
  - Quản lý đơn hàng: `admin/orders`
  - Tạo đơn hàng mới: `admin/orders/add`
  - Sửa đơn hàng: `admin/orders/edit`
  - Chi tiết đơn hàng: `admin/orders/detail`
  - Cập nhật trạng thái đơn hàng: `admin/orders/update`

- **Component/Views chính:** 
  - Layout dùng chung: `includes/header.php`, `includes/footer.php`
  - Các view nghiệp vụ riêng: Được lưu trữ cấu trúc hóa trong thư mục `Views/` của từng Module nghiệp vụ (ví dụ: `app/Modules/Auth/Views/`, `app/Modules/Admin/Views/`, v.v.).

---

## 9. 🧪 KIỂM THỬ, TRIỂN KHAI & BẢO MẬT
- **Unit/Integration Test:** Thực hiện kiểm thử thủ công (Manual Testing) trực tiếp qua trình duyệt web trên localhost.
- **CI/CD Pipeline:** Chưa tích hợp tự động, triển khai thủ công mã nguồn lên server.
- **Môi trường:** 
  - Phát triển (Dev Environment): Localhost (sử dụng PHP built-in server hoặc phần mềm giả lập Apache/MySQL như XAMPP, Laragon).
  - Production (Sản xuất): VPS hoặc Hosting Linux hỗ trợ chạy dịch vụ Apache/Nginx, PHP >= 8.0 và cơ sở dữ liệu MySQL.
- **Bảo mật đã áp dụng:**
  - Ngăn chặn triệt để lỗi SQL Injection bằng cơ chế prepared statements của PDO.
  - Ngăn ngừa lỗi XSS thông qua việc sử dụng helper `sanitize()` để lọc dữ liệu đầu vào và hàm `htmlspecialchars()` khi render các thông báo động (như toast, flash messages).
  - Cơ chế phòng chống tấn công giả mạo yêu cầu chéo trang CSRF bằng Token lưu trong Session và so khớp chặt chẽ trên mọi form POST.
  - Thiết lập thuộc tính an toàn Cookie Session (`HttpOnly`, `SameSite=Lax`) chống đánh cắp session id qua các mã độc JS.
  - Sử dụng thuật toán chuẩn `bcrypt` thông qua hàm `password_hash()` để mã hóa an toàn mật khẩu người dùng trước khi lưu DB.
  - Hạn chế brute-force tấn công đăng nhập nhờ cơ chế Rate Limiter kiểm tra IP.
  - Triển khai file `.env` chứa các thông số cấu hình nhạy cảm nằm ngoài thư mục public để tránh lộ lọt thông tin cấu hình database.

---

## 10. 📝 YÊU CẦU ĐẶC BIỆT CHO BÁO CÁO WORD
- **Giữ nguyên văn phong học thuật ("em/sinh viên") hay chuyển sang kỹ thuật ("hệ thống được thiết kế...")?** `Chuyển sang văn phong kỹ thuật ("hệ thống được thiết kế...", "quản trị viên thực hiện...") để đảm bảo tính chuyên nghiệp của tài liệu đặc tả hệ thống thực tế.`
- **Trường có yêu cầu giữ trang bìa, lời cam đoan, mục lục cố định không?** `Không có yêu cầu cố định, được tùy biến thiết kế linh hoạt khoa học.`
- **Có cần chèn ảnh screenshot thực tế từ hệ thống không?** `Có. Cần chèn hình ảnh minh hoạ thực tế cho các giao diện trọng yếu: Trang chủ, Danh sách sản phẩm, Giỏ hàng, Trang đặt hàng (Thanh toán), Trang Quản lý Đơn hàng và Dashboard Admin.`
- **Yêu cầu khác:** `Tạo và thiết kế các sơ đồ UML (Usecase sơ đồ, Activity sơ đồ, Sequence sơ đồ của luồng mua hàng và xác thực, sơ đồ ERD lớp dữ liệu) chuẩn hóa theo mã nguồn Custom MVC của dự án.`

---
✅ **Sau khi điền xong, reply lại nội dung file này. Tôi sẽ:**
1. Generate sơ đồ UML (Use Case, Activity, Sequence, ERD) dưới dạng code Mermaid hoặc mô tả chi tiết để bạn vẽ trong StarUML/Draw.io.
2. Viết lại toàn bộ nội dung Word (Chương 1-4) khớp 100% với code thực tế.
3. Loại bỏ hoàn toàn phần "demo tĩnh", "tương lai PHP/MySQL", "chi phí giả định".