import time
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select

def setup_driver():
    """Thiết lập Chrome driver"""
    options = Options()
    options.add_argument("--start-maximized")
    options.add_argument("--disable-web-security")
    options.add_argument("--allow-running-insecure-content")
    return webdriver.Chrome(executable_path="chromedriver.exe", options=options)

def test_admin_login(driver):
    """Test trang đăng nhập admin hiển thị"""
    print("=== TEST TRANG ĐĂNG NHẬP ADMIN ===")

    driver.get("http://localhost/office-supplies/admin/login.php")

    # Kiểm tra trang đăng nhập hiển thị
    wait = WebDriverWait(driver, 10)
    try:
        username_field = wait.until(EC.presence_of_element_located((By.ID, "username")))
        password_field = wait.until(EC.presence_of_element_located((By.ID, "password")))
        login_button = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "button[type='submit']")))

        if username_field and password_field and login_button:
            print("✅ Trang đăng nhập admin hiển thị đầy đủ")
            return True
        else:
            print("❌ Thiếu các trường đăng nhập")
            return False
    except:
        print("❌ Không thể load trang đăng nhập admin")
        return False

def test_admin_products(driver):
    """Test truy cập trang admin sản phẩm (sẽ redirect về login nếu chưa đăng nhập)"""
    print("\n=== TEST TRUY CẬP TRANG ADMIN SẢN PHẨM ===")

    driver.get("http://localhost/office-supplies/admin/products.php")
    wait = WebDriverWait(driver, 10)

    try:
        # Nếu chưa đăng nhập, sẽ redirect về login hoặc hiển thị thông báo
        time.sleep(1)
        if "login" in driver.current_url or "đăng nhập" in driver.page_source.lower():
            print("✅ Trang admin sản phẩm yêu cầu đăng nhập (bình thường)")
            return True
        else:
            # Nếu có thể truy cập được, kiểm tra có tiêu đề sản phẩm
            try:
                page_title = driver.find_element(By.TAG_NAME, "h1")
                if "sản phẩm" in page_title.text.lower():
                    print("✅ Trang quản lý sản phẩm admin hiển thị")
                    return True
                else:
                    print("❌ Tiêu đề trang không đúng")
                    return False
            except:
                print("❌ Không thể xác định trạng thái trang")
                return False
    except Exception as e:
        print(f"❌ Lỗi khi truy cập trang admin sản phẩm: {e}")
        return False

def test_admin_categories(driver):
    """Test truy cập trang admin danh mục"""
    print("\n=== TEST TRUY CẬP TRANG ADMIN DANH MỤC ===")

    driver.get("http://localhost/office-supplies/admin/categories.php")
    wait = WebDriverWait(driver, 10)

    try:
        time.sleep(1)
        if "login" in driver.current_url or "đăng nhập" in driver.page_source.lower():
            print("✅ Trang admin danh mục yêu cầu đăng nhập (bình thường)")
            return True
        else:
            try:
                page_title = driver.find_element(By.TAG_NAME, "h1")
                if "danh mục" in page_title.text.lower():
                    print("✅ Trang quản lý danh mục admin hiển thị")
                    return True
                else:
                    print("❌ Tiêu đề trang không đúng")
                    return False
            except:
                print("❌ Không thể xác định trạng thái trang")
                return False
    except Exception as e:
        print(f"❌ Lỗi khi truy cập trang admin danh mục: {e}")
        return False

def test_admin_brands(driver):
    """Test truy cập trang admin thương hiệu"""
    print("\n=== TEST TRUY CẬP TRANG ADMIN THƯƠNG HIỆU ===")

    driver.get("http://localhost/office-supplies/admin/brands.php")
    wait = WebDriverWait(driver, 10)

    try:
        time.sleep(1)
        if "login" in driver.current_url or "đăng nhập" in driver.page_source.lower():
            print("✅ Trang admin thương hiệu yêu cầu đăng nhập (bình thường)")
            return True
        else:
            try:
                page_title = driver.find_element(By.TAG_NAME, "h1")
                if "thương hiệu" in page_title.text.lower() or "brand" in page_title.text.lower():
                    print("✅ Trang quản lý thương hiệu admin hiển thị")
                    return True
                else:
                    print("❌ Tiêu đề trang không đúng")
                    return False
            except:
                print("❌ Không thể xác định trạng thái trang")
                return False
    except Exception as e:
        print(f"❌ Lỗi khi truy cập trang admin thương hiệu: {e}")
        return False

def test_admin_users(driver):
    """Test truy cập trang admin người dùng"""
    print("\n=== TEST TRUY CẬP TRANG ADMIN NGƯỜI DÙNG ===")

    driver.get("http://localhost/office-supplies/admin/users.php")
    wait = WebDriverWait(driver, 10)

    try:
        time.sleep(1)
        if "login" in driver.current_url or "đăng nhập" in driver.page_source.lower():
            print("✅ Trang admin người dùng yêu cầu đăng nhập (bình thường)")
            return True
        else:
            try:
                page_title = driver.find_element(By.TAG_NAME, "h1")
                if "người dùng" in page_title.text.lower() or "user" in page_title.text.lower():
                    print("✅ Trang quản lý người dùng admin hiển thị")
                    return True
                else:
                    print("❌ Tiêu đề trang không đúng")
                    return False
            except:
                print("❌ Không thể xác định trạng thái trang")
                return False
    except Exception as e:
        print(f"❌ Lỗi khi truy cập trang admin người dùng: {e}")
        return False

def test_admin_orders(driver):
    """Test truy cập trang admin đơn hàng"""
    print("\n=== TEST TRUY CẬP TRANG ADMIN ĐƠN HÀNG ===")

    driver.get("http://localhost/office-supplies/admin/orders.php")
    wait = WebDriverWait(driver, 10)

    try:
        time.sleep(1)
        if "login" in driver.current_url or "đăng nhập" in driver.page_source.lower():
            print("✅ Trang admin đơn hàng yêu cầu đăng nhập (bình thường)")
            return True
        else:
            try:
                page_title = driver.find_element(By.TAG_NAME, "h1")
                if "đơn hàng" in page_title.text.lower() or "order" in page_title.text.lower():
                    print("✅ Trang quản lý đơn hàng admin hiển thị")
                    return True
                else:
                    print("❌ Tiêu đề trang không đúng")
                    return False
            except:
                print("❌ Không thể xác định trạng thái trang")
                return False
    except Exception as e:
        print(f"❌ Lỗi khi truy cập trang admin đơn hàng: {e}")
        return False

def test_user_registration(driver):
    """Test trang đăng ký người dùng hiển thị"""
    print("\n=== TEST TRANG ĐĂNG KÝ NGƯỜI DÙNG ===")

    driver.get("http://localhost/office-supplies/user/register.php")
    wait = WebDriverWait(driver, 10)

    try:
        username_field = wait.until(EC.presence_of_element_located((By.NAME, "username")))
        email_field = wait.until(EC.presence_of_element_located((By.NAME, "email")))
        password_field = wait.until(EC.presence_of_element_located((By.NAME, "password")))
        confirm_field = wait.until(EC.presence_of_element_located((By.NAME, "confirm_password")))
        register_button = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "button[type='submit']")))

        if username_field and email_field and password_field and confirm_field and register_button:
            print("✅ Trang đăng ký người dùng hiển thị đầy đủ")
            return True
        else:
            print("❌ Thiếu các trường đăng ký")
            return False
    except Exception as e:
        print(f"❌ Lỗi khi load trang đăng ký: {e}")
        return False

def test_user_login(driver):
    """Test trang đăng nhập người dùng hiển thị"""
    print("\n=== TEST TRANG ĐĂNG NHẬP NGƯỜI DÙNG ===")

    driver.get("http://localhost/office-supplies/user/login.php")

    try:
        wait = WebDriverWait(driver, 10)
        username_field = wait.until(EC.presence_of_element_located((By.NAME, "username")))
        password_field = wait.until(EC.presence_of_element_located((By.NAME, "password")))
        login_button = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "button[type='submit']")))

        if username_field and password_field and login_button:
            print("✅ Trang đăng nhập người dùng hiển thị đầy đủ")
            return True
        else:
            print("❌ Thiếu các trường đăng nhập")
            return False
    except Exception as e:
        print(f"❌ Lỗi khi load trang đăng nhập: {e}")
        return False

def test_user_search_products(driver):
    """Test tìm kiếm sản phẩm người dùng"""
    print("\n=== TEST TÌM KIẾM SẢN PHẨM NGƯỜI DÙNG ===")

    driver.get("http://localhost/office-supplies/user/products.php")
    wait = WebDriverWait(driver, 10)

    try:
        # Test tìm kiếm
        search_input = driver.find_element(By.NAME, "q")
        search_input.clear()
        search_input.send_keys("bút")
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()

        time.sleep(1)
        if "bút" in driver.page_source.lower():
            print("✅ Tìm kiếm sản phẩm người dùng hoạt động")
            return True
        else:
            print("❌ Tìm kiếm sản phẩm người dùng không hoạt động")
            return False
    except Exception as e:
        print(f"❌ Lỗi khi tìm kiếm: {e}")
        return False

def test_user_add_to_cart(driver):
    """Test trang sản phẩm có nút thêm vào giỏ"""
    print("\n=== TEST NÚT THÊM VÀO GIỎ HÀNG ===")

    driver.get("http://localhost/office-supplies/user/products.php")

    try:
        wait = WebDriverWait(driver, 10)
        # Kiểm tra có ít nhất một nút thêm vào giỏ
        add_buttons = wait.until(EC.presence_of_all_elements_located((By.CSS_SELECTOR, "button[name='add_to_cart']")))

        if len(add_buttons) > 0:
            print(f"✅ Tìm thấy {len(add_buttons)} nút thêm vào giỏ hàng")
            return True
        else:
            print("❌ Không tìm thấy nút thêm vào giỏ")
            return False
    except Exception as e:
        print(f"❌ Lỗi khi kiểm tra nút thêm vào giỏ: {e}")
        return False

def test_user_cart(driver):
    """Test xem giỏ hàng"""
    print("\n=== TEST XEM GIỎ HÀNG ===")

    driver.get("http://localhost/office-supplies/user/cart.php")

    try:
        wait = WebDriverWait(driver, 10)
        page_title = wait.until(EC.presence_of_element_located((By.TAG_NAME, "h1")))

        if "giỏ hàng" in page_title.text.lower() or "cart" in page_title.text.lower():
            print("✅ Trang giỏ hàng hiển thị")
            return True
        else:
            print("❌ Tiêu đề trang giỏ hàng không đúng")
            return False
    except Exception as e:
        print(f"❌ Lỗi khi xem giỏ hàng: {e}")
        return False

def run_all_tests():
    """Chạy tất cả các test"""
    print("🚀 BẮT ĐẦU TEST TẤT CẢ CHỨC NĂNG TRANG WEB OFFICE-SUPPLIES")
    print("=" * 60)

    driver = setup_driver()
    results = {}

    try:
        # Test Admin - chỉ test UI
        results['admin_login'] = test_admin_login(driver)
        results['admin_products'] = test_admin_products(driver)
        results['admin_categories'] = test_admin_categories(driver)
        results['admin_brands'] = test_admin_brands(driver)
        results['admin_users'] = test_admin_users(driver)
        results['admin_orders'] = test_admin_orders(driver)

        # Test User
        results['user_registration'] = test_user_registration(driver)
        results['user_login'] = test_user_login(driver)
        results['user_search'] = test_user_search_products(driver)
        results['user_add_cart'] = test_user_add_to_cart(driver)
        results['user_cart'] = test_user_cart(driver)

    except Exception as e:
        print(f"❌ Lỗi tổng thể: {e}")
    finally:
        driver.quit()

    # Tổng kết kết quả
    print("\n" + "=" * 60)
    print("📊 TỔNG KẾT KẾT QUẢ TEST")

    passed = 0
    total = len(results)

    for test_name, result in results.items():
        status = "✅ PASS" if result else "❌ FAIL"
        print(f"{test_name}: {status}")
        if result:
            passed += 1

    print(f"\n🎯 KẾT QUẢ: {passed}/{total} test thành công")

    if passed == total:
        print("🎉 TẤT CẢ TEST ĐỀU THÀNH CÔNG!")
    elif passed >= total * 0.8:
        print("👍 Hầu hết test thành công!")
    else:
        print("⚠️  Cần kiểm tra lại các chức năng thất bại!")

if __name__ == "__main__":
    run_all_tests()