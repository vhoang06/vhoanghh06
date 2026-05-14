import time
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def run_search_test():
    base_url = "http://localhost/office-supplies/user/products.php"
    print("--- BẮT ĐẦU TEST CHỨC NĂNG TÌM KIẾM ---")

    # Sử dụng ChromeDriver đã tải
    options = Options()
    options.add_argument("--start-maximized")
    options.add_argument("--disable-web-security")
    options.add_argument("--allow-running-insecure-content")
    driver = webdriver.Chrome(executable_path="chromedriver.exe", options=options)
    wait = WebDriverWait(driver, 10)

    try:
        driver.get(base_url)

        print("1. Kiểm tra trang sản phẩm")
        page_title = wait.until(EC.visibility_of_element_located((By.TAG_NAME, "h1")))
        if "sản phẩm" in page_title.text.lower():
            print("   ✅ Trang sản phẩm hiển thị chính xác.")
        else:
            print(f"   ❌ Tiêu đề sai. Mong đợi chứa 'sản phẩm', Thực tế: {page_title.text}")

        print("2. Thực hiện tìm kiếm từ khóa 'bàn'")
        search_input = driver.find_element(By.NAME, "q")
        search_input.clear()
        search_input.send_keys("bàn")
        search_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        search_button.click()

        wait.until(EC.text_to_be_present_in_element((By.TAG_NAME, "body"), "bàn"))
        print("   ✅ Đã thực hiện tìm kiếm.")

        print("3. Kiểm tra kết quả tìm kiếm")
        page_text = driver.page_source.lower()

        if "bàn ghế" in page_text and "bàn học" in page_text:
            print("   ✅ Tìm thấy sản phẩm khớp: 'bàn ghế', 'bàn học'")
        else:
            print("   ❌ Không tìm thấy sản phẩm mong đợi.")

        if 'value="bàn"' in driver.page_source:
            print("   ✅ Từ khóa được giữ lại trong ô tìm kiếm.")
        else:
            print("   ❌ Từ khóa không được giữ lại.")

        if "không tìm thấy sản phẩm nào phù hợp" not in page_text:
            print("   ✅ Không có thông báo 'không tìm thấy sản phẩm'.")
        else:
            print("   ❌ Hiển thị thông báo không tìm thấy sản phẩm.")

        print("\n✅ KẾT QUẢ: TEST TÌM KIẾM HOÀN TẤT.")

    except Exception as e:
        print(f"❌ Lỗi: {e}")
    finally:
        driver.quit()

if __name__ == "__main__":
    run_search_test()
