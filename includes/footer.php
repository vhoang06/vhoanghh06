<?php // includes/footer.php ?>
</main>

<footer class="bg-dark text-white pt-5 mt-5">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <div class="d-flex align-items-center mb-3">
                    <a href="index.php?route=home" class="text-white text-decoration-none d-flex align-items-center" style="font-weight: bold; font-size: 1.25rem; gap: 0.75rem;">
                        <span class="brand-logo" style="width: 40px; height: 40px; display: inline-block; overflow: hidden; border-radius: 4px;">
                            <img src="assets/images/logo.jpg" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
                        </span>
                        <span>Office Supplies</span>
                    </a>
                </div>
                <p class="small text-muted mb-3">Chuyên cung cấp văn phòng phẩm, bàn ghế, thiết bị và phụ kiện văn phòng.</p>
                <p class="small mb-0"><i class="fas fa-map-marker-alt me-2"></i>741 Giải Phóng, Hoàng Mai, Hà Nội</p>
                <p class="small mb-0"><i class="fas fa-phone-alt me-2"></i>036 995 1001</p>
                <p class="small mb-0"><i class="fas fa-envelope me-2"></i>viethoangk651@gmail.com</p>
                <p class="small mb-0 mt-2"><strong>Kết nối với chúng tôi:</strong></p>
                <p class="mb-0">
                    <a href="https://facebook.com/" target="_blank" class="d-inline-flex align-items-center text-white text-decoration-none">
                        <img src="assets/images/facebook.svg" alt="Facebook" style="width:28px; height:28px; margin-right:0.5rem;">
                        <span>Facebook</span>
                    </a>
                </p>
            </div>
            <div class="col-md-4">
                <h5 class="text-white mb-3">Liên kết nhanh</h5>
                <ul class="list-unstyled small">
                    <li><a href="index.php?route=home" class="text-white text-decoration-none">Trang chủ</a></li>
                    <li><a href="index.php?route=products" class="text-white text-decoration-none">Sản phẩm</a></li>
                    <li><a href="index.php?route=contact" class="text-white text-decoration-none">Liên hệ</a></li>
                    <li><a href="index.php?route=orders" class="text-white text-decoration-none">Đơn hàng</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="text-white mb-3">Giờ mở cửa</h5>
                <p class="small mb-1">Thứ Hai - Thứ Sáu: 08:00 - 18:00</p>
                <p class="small mb-1">Thứ Bảy: 08:00 - 12:00</p>
                <p class="small mb-0">Chủ nhật nghỉ</p>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pb-3">
            <p class="mb-2 mb-md-0 small">© <?= date('Y') ?> Office Supplies. All rights reserved.</p>
            <p class="mb-0 small text-muted">Designed with ❤️ for learning purpose</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>