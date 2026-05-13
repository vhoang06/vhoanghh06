<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold">Liên hệ</h1>
            <p class="text-muted">Chúng tôi luôn sẵn sàng lắng nghe ý kiến từ bạn</p>
        </div>

        <div class="row g-5">
            <!-- Thông tin liên hệ -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-primary text-white">
                    <h4 class="fw-bold mb-4">Thông tin liên hệ</h4>
                    
                    <div class="d-flex gap-3 mb-4">
                        <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Địa chỉ</h6>
                            <p class="mb-0 opacity-75">741 Giải Phóng, Hoàng Mai, Hà Nội</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Điện thoại</h6>
                            <p class="mb-0 opacity-75">036 995 1001</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">Email</h6>
                            <p class="mb-0 opacity-75">viethoangk651@gmail.com</p>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-top border-white border-opacity-10">
                        <h6 class="fw-bold mb-3">Theo dõi chúng tôi</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-sm btn-light rounded-circle" style="width: 35px; height: 35px;"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-sm btn-light rounded-circle" style="width: 35px; height: 35px;"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn btn-sm btn-light rounded-circle" style="width: 35px; height: 35px;"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form liên hệ -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <form action="index.php?route=contact/submit" method="POST">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ và tên</label>
                                <input type="text" name="name" class="form-control" required placeholder="Nguyễn Văn A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" required placeholder="example@mail.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tiêu đề</label>
                                <input type="text" name="subject" class="form-control" required placeholder="Tôi cần hỗ trợ về...">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nội dung</label>
                                <textarea name="message" class="form-control" rows="5" required placeholder="Nhập tin nhắn của bạn ở đây..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill mt-2">Gửi tin nhắn</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bản đồ giả lập -->
        <div class="mt-5 pt-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px; color: #adb5bd;">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt fa-4x mb-3"></i>
                        <p class="mb-0 fw-bold">Bản đồ sẽ được hiển thị tại đây</p>
                        <p class="small">Google Maps Integration</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
