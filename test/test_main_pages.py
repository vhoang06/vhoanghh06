import time
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def setup_driver():
    """Thiết lập Chrome driver"""
    options = Options()
    options.add_argument("--start-maximized")
    options.add_argument("--disable-web-security")
    options.add_argument("--allow-running-insecure-content")
    return webdriver.Chrome(executable_path="chromedriver.exe", options=options)

def test_main_pages():
    """Test các trang chính của website"""
    print("🚀 BẮT ĐẦU TEST CÁC TRANG CHÍNH")
    print("=" * 50)

    driver = setup_driver()
    results = {}

    try:
        # Test trang chủ user
        print("Testing user homepage...")
        driver.get("http://localhost/office-supplies/user/index.php")
        wait = WebDriverWait(driver, 5)
        try:
            page_title = wait.until(EC.presence_of_element_located((By.TAG_NAME, "h1")))
            results['user_home'] = True
            print("✅ User homepage: OK")
        except:
            results['user_home'] = False
            print("❌ User homepage: FAIL")

        # Test trang sản phẩm
        print("Testing products page...")
        driver.get("http://localhost/office-supplies/user/products.php")
        try:
            search_box = wait.until(EC.presence_of_element_located((By.NAME, "q")))
            results['products_page'] = True
            print("✅ Products page: OK")
        except:
            results['products_page'] = False
            print("❌ Products page: FAIL")

        # Test tìm kiếm
        print("Testing search functionality...")
        try:
            search_input = driver.find_element(By.NAME, "q")
            search_input.clear()
            search_input.send_keys("bút")
            driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
            time.sleep(1)
            results['search'] = True
            print("✅ Search: OK")
        except:
            results['search'] = False
            print("❌ Search: FAIL")

        # Test trang đăng nhập user
        print("Testing user login page...")
        driver.get("http://localhost/office-supplies/user/login.php")
        try:
            username_field = wait.until(EC.presence_of_element_located((By.NAME, "username")))
            results['user_login'] = True
            print("✅ User login page: OK")
        except:
            results['user_login'] = False
            print("❌ User login page: FAIL")

        # Test trang đăng ký user
        print("Testing user register page...")
        driver.get("http://localhost/office-supplies/user/register.php")
        try:
            email_field = wait.until(EC.presence_of_element_located((By.NAME, "email")))
            results['user_register'] = True
            print("✅ User register page: OK")
        except:
            results['user_register'] = False
            print("❌ User register page: FAIL")

        # Test trang admin login
        print("Testing admin login page...")
        driver.get("http://localhost/office-supplies/admin/login.php")
        try:
            admin_username = wait.until(EC.presence_of_element_located((By.ID, "username")))
            results['admin_login'] = True
            print("✅ Admin login page: OK")
        except:
            results['admin_login'] = False
            print("❌ Admin login page: FAIL")

        # Test trang giỏ hàng
        print("Testing cart page...")
        driver.get("http://localhost/office-supplies/user/cart.php")
        try:
            cart_title = wait.until(EC.presence_of_element_located((By.TAG_NAME, "h1")))
            results['cart'] = True
            print("✅ Cart page: OK")
        except:
            results['cart'] = False
            print("❌ Cart page: FAIL")

    except Exception as e:
        print(f"❌ Lỗi tổng thể: {e}")
    finally:
        driver.quit()

    # Tổng kết
    print("\n" + "=" * 50)
    print("📊 TỔNG KẾT")

    passed = sum(1 for result in results.values() if result)
    total = len(results)

    for test_name, result in results.items():
        status = "✅ PASS" if result else "❌ FAIL"
        print(f"{test_name}: {status}")

    print(f"\n🎯 KẾT QUẢ: {passed}/{total} test thành công")

    if passed == total:
        print("🎉 TẤT CẢ TEST ĐỀU THÀNH CÔNG!")
    elif passed >= total * 0.8:
        print("👍 Hầu hết test thành công!")
    else:
        print("⚠️  Cần kiểm tra lại các trang thất bại!")

if __name__ == "__main__":
    test_main_pages()