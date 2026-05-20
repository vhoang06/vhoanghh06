# 📋 PHẦN 4: ĐẶC TẢ YÊU CẦU VÀ SƠ ĐỒ USECASE
## DỰ ÁN: WEBSITE THƯƠNG MẠI ĐIỆN TỬ VĂN PHÒNG PHẨM (OFFICE SUPPLIES)

---

## 1. Phân tích Tác nhân (Actors)

Hệ thống định nghĩa 3 tác nhân chính tương tác với ứng dụng:

1.  **Khách vãng lai (Guest):** Người dùng chưa có tài khoản hoặc chưa đăng nhập vào hệ thống. Họ chỉ có quyền xem thông tin cơ bản và duyệt qua các sản phẩm.
2.  **Khách hàng (Customer):** Người dùng đã đăng ký tài khoản và đăng nhập thành công. Họ kế thừa toàn bộ quyền của Khách vãng lai và có thêm các tính năng thương mại (Đặt hàng, quản lý đơn hàng).
3.  **Quản trị viên (Admin):** Người dùng có quyền cao nhất (`role = 'admin'`), chịu trách nhiệm quản lý nội dung sản phẩm, danh mục, kiểm soát đơn đặt hàng và quản lý tài khoản người dùng trên hệ thống.

---

## 2. Đặc tả Yêu cầu Chức năng (Usecase Specifications)

### 2.1. Phân hệ Khách hàng & Khách vãng lai (Client-side)

*   **UC01: Đăng ký tài khoản mới**
    *   *Mô tả:* Cho phép khách vãng lai đăng ký tài khoản khách hàng trên hệ thống. Sau khi đăng ký, hệ thống gửi email kèm mã token để xác thực tài khoản trong vòng 24h.
*   **UC02: Đăng nhập hệ thống**
    *   *Mô tả:* Người dùng đăng nhập bằng tài khoản và mật khẩu. Tích hợp Rate Limiting để ngăn chặn Brute-force (sai 5 lần liên tiếp sẽ bị khóa 5 phút per IP).
*   **UC03: Quên mật khẩu & Đặt lại mật khẩu**
    *   *Mô tả:* Người dùng điền email yêu cầu cấp lại mật khẩu. Hệ thống kiểm tra và gửi email chứa token reset mật khẩu có hạn dùng trong 1h.
*   **UC04: Duyệt và Tìm kiếm sản phẩm**
    *   *Mô tả:* Cho phép người dùng duyệt danh sách sản phẩm, lọc nhanh theo thương hiệu sản xuất hoặc theo danh mục hàng hóa, và tìm kiếm sản phẩm theo tên.
*   **UC05: Xem chi tiết sản phẩm**
    *   *Mô tả:* Xem thông tin chi tiết của một sản phẩm bao gồm tên, hình ảnh, đơn giá, số lượng còn trong kho và mô tả chi tiết.
*   **UC06: Quản lý giỏ hàng**
    *   *Mô tả:* Người dùng có thể thêm sản phẩm vào giỏ hàng, cập nhật số lượng đặt mua hoặc loại bỏ sản phẩm ra khỏi giỏ.
*   **UC07: Gửi biểu mẫu liên hệ**
    *   *Mô tả:* Người dùng có thể gửi thắc mắc, phản hồi hoặc yêu cầu hỗ trợ tới ban quản trị thông qua biểu mẫu trang liên hệ.
*   **UC08: Đặt hàng & Checkout (Thanh toán COD)**
    *   *Mô tả:* Khách hàng tiến hành nhập thông tin nhận hàng (Họ tên, số điện thoại, địa chỉ nhận hàng) và xác nhận đặt hàng với hình thức nhận hàng thanh toán (COD). Hệ thống sẽ tự động trừ đi số lượng sản phẩm tương ứng trong kho hàng.
*   **UC09: Xem lịch sử mua hàng**
    *   *Mô tả:* Khách hàng có thể truy cập danh sách các đơn hàng đã đặt để theo dõi trạng thái đơn hàng (Chờ duyệt, Đang xử lý, Hoàn thành, Hủy).

### 2.2. Phân hệ Quản trị (Admin-side)

*   **UC11: Đăng nhập trang quản trị**
    *   *Mô tả:* Người quản trị đăng nhập vào phân hệ Backend chuyên biệt để làm việc.
*   **UC12: Xem dashboard thống kê**
    *   *Mô tả:* Xem các số liệu tổng hợp trực quan như doanh thu bán hàng, tổng số đơn đặt hàng và số lượng tài khoản đăng ký mới.
*   **UC13: Quản lý Sản phẩm (CRUD Product)**
    *   *Mô tả:* Admin có quyền Thêm mới sản phẩm (kèm tải ảnh lên thư mục lưu trữ), Sửa thông tin sản phẩm, Cập nhật tồn kho hoặc Xóa bỏ sản phẩm khỏi hệ thống.
*   **UC14: Quản lý Danh mục (CRUD Category)**
    *   *Mô tả:* Thêm, sửa, xóa các danh mục sản phẩm (VD: Đồ học tập, Đồ văn phòng...).
*   **UC15: Quản lý Thương hiệu (CRUD Brand)**
    *   *Mô tả:* Thêm, sửa, xóa các nhãn hàng/thương hiệu sản xuất văn phòng phẩm.
*   **UC16: Quản lý & Xử lý Đơn hàng**
    *   *Mô tả:* Admin duyệt danh sách toàn bộ đơn hàng của hệ thống và tiến hành cập nhật trạng thái đơn hàng theo tiến trình xử lý thực tế (Chờ xử lý -> Đang giao -> Hoàn thành / Hủy bỏ).
*   **UC17: Quản lý Người dùng**
    *   *Mô tả:* Admin theo dõi danh sách tất cả các tài khoản đăng ký trên hệ thống, thời gian đăng nhập cuối cùng và có thể phân quyền tài khoản (User hoặc Admin).

---

## 3. Sơ đồ Usecase Tổng quát (Usecase Diagram)

Sơ đồ Usecase dưới đây mô tả sự tương tác giữa các tác nhân và các chức năng của hệ thống:

```mermaid
graph TD
    %% Định nghĩa các Actor
    Guest[Khách vãng lai]
    Customer[Khách hàng đã đăng ký]
    Admin[Quản trị viên]

    %% Kế thừa tác nhân
    Customer -->|Kế thừa| Guest

    %% Usecase Khách vãng lai
    subgraph Frontend - Khách hàng
        UC01(UC01: Đăng ký tài khoản)
        UC02(UC02: Đăng nhập)
        UC03(UC03: Quên/Đặt lại mật khẩu)
        UC04(UC04: Tìm kiếm/Lọc sản phẩm)
        UC05(UC05: Xem chi tiết sản phẩm)
        UC06(UC06: Quản lý giỏ hàng)
        UC07(UC07: Gửi liên hệ)
        UC08(UC08: Đặt hàng & Checkout COD)
        UC09(UC09: Xem lịch sử đơn hàng)
    end

    %% Usecase Admin
    subgraph Backend - Quản trị (Admin Panel)
        UC11(UC11: Đăng nhập Admin)
        UC12(UC12: Xem Dashboard Thống kê)
        UC13(UC13: Quản lý Sản phẩm CRUD)
        UC14(UC14: Quản lý Danh mục CRUD)
        UC15(UC15: Quản lý Thương hiệu CRUD)
        UC16(UC16: Quản lý & Cập nhật Đơn hàng)
        UC17(UC17: Quản lý Người dùng)
    end

    %% Liên kết Actor và Usecase
    Guest --> UC01
    Guest --> UC02
    Guest --> UC03
    Guest --> UC04
    Guest --> UC05
    Guest --> Guest --> UC06
    Guest --> UC07

    Customer --> UC08
    Customer --> UC09

    Admin --> UC11
    Admin --> UC12
    Admin --> UC13
    Admin --> UC14
    Admin --> UC15
    Admin --> UC16
    Admin --> UC17
```
