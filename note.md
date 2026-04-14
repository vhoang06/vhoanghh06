# 📋 Office Supplies - Changelog

## v1.1 - Security & Features Upgrade (2026-04-15)

### 🛡️ Bảo mật mới

| Tính năng | Mô tả |
|---|---|
| **CSRF Protection** | Token trên mọi POST form (login, register, cart, checkout, admin CRUD) |
| **Rate Limiting** | Giới hạn 5 lần thử login/5 phút per IP |
| **Email Verification** | Token 24h, tự động gửi mail sau đăng ký |
| **Password Reset** | Token 1h, gửi mail → đặt lại mật khẩu |
| **Session Security** | `httponly` + `samesite=Lax` cho cookie |
| **Input Sanitizer** | Helper `sanitize()`, `isValidEmail()`, `isValidPassword()` |
| **XSS Fix** | Escape toast message trong admin/products.php |
| **Migration Protection** | Chỉ chạy từ localhost/CLI |
| **.env Pattern** | Dời DB credentials ra file riêng |

### ✨ Tính năng mới

| Tính năng | File |
|---|---|
| **Quên mật khẩu** | `forgot_password.php`, `reset_password.php` |
| **Xác thực email** | `verify_email.php` |
| **Brand filter** | `products.php` - lọc theo thương hiệu |
| **Flash messages** | `includes/flash.php` - thông báo giữa request |
| **Last login tracking** | Tự động update `last_login` khi login |
| **Mailer helper** | `includes/mailer.php` - sendMail() + templates |

### 🐛 Bug fixes

| Bug | Mô tả |
|---|---|
| Brand filter không hoạt động | `brands.php` link đến `?brand=X` nhưng `products.php` không handle |
| CSRF thiếu trên add_to_cart | Form "Thêm vào giỏ" ở `index.php` + `products.php` |
| Existing users bị block login | SQL dump không set `email_verified=1` |
| XSS trong toast message | `implode()` không escape error messages |

### 📁 File mới tạo (10 files)

| File | Chức năng |
|---|---|
| `includes/env.php` | Load `.env` → define() constants |
| `includes/csrf.php` | CSRF: token, field, validate |
| `includes/security.php` | Rate limiter, sanitizer, validators |
| `includes/flash.php` | Flash messages system |
| `includes/mailer.php` | Mail helper + email templates |
| `verify_email.php` | Trang xác thực email |
| `forgot_password.php` | Trang quên mật khẩu |
| `reset_password.php` | Trang đặt lại mật khẩu |
| `migration_security.php` | Migration bảo mật (localhost only) |
| `.env.example` | Template cấu hình |

### 📁 File sửa đổi (17 files)

`includes/config.php` · `includes/header.php` · `login.php` · `register.php` · `cart.php` · `checkout.php` · `contact.php` · `index.php` · `products.php` · `admin/login.php` · `admin/products.php` · `admin/orders.php` · `admin/users.php` · `admin/categories.php` · `admin/brands.php` · `office_supplies.sql` · `.gitignore`

---

## v1.0 - Original (2026-02-07)

Website thương mại điện tử bán văn phòng phẩm cơ bản:
- User: đăng ký, đăng nhập, giỏ hàng, checkout COD, xem đơn hàng, liên hệ
- Admin: dashboard, CRUD sản phẩm/danh mục/thương hiệu, quản lý đơn hàng, quản lý user
- Database: MySQL/MariaDB, 6 bảng
- Frontend: Bootstrap 5, Font Awesome
