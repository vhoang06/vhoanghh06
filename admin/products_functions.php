<?php
// admin/products_functions.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$upload_dir = __DIR__ . '/../assets/images/products/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

function moveUploadedFile($tmp_name, $target) {
    if (function_exists('is_uploaded_file') && is_uploaded_file($tmp_name)) {
        return move_uploaded_file($tmp_name, $target);
    }
    return rename($tmp_name, $target);
}

function uploadImage($file) {
    global $upload_dir;
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2*1024*1024) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) return null;
    $filename = uniqid('prod_') . '.' . $ext;
    $target = $upload_dir . $filename;
    if (moveUploadedFile($file['tmp_name'], $target)) return 'assets/images/products/' . $filename;
    return null;
}
