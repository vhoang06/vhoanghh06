<?php
// includes/auth_functions.php

function validateRegistrationData($username, $email, $password, $confirm) {
    $errors = [];
    if (empty($username)) {
        $errors[] = "Tên đăng nhập không được để trống";
    }
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    if (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
    }
    if ($password !== $confirm) {
        $errors[] = "Mật khẩu xác nhận không khớp";
    }
    return $errors;
}

function isDuplicateUser($pdo, $username, $email) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    return (bool) $stmt->fetch();
}

function createUser($pdo, $username, $email, $password) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashed])) {
        return (int) $pdo->lastInsertId();
    }
    return 0;
}

function authenticateUser($pdo, $usernameOrEmail, $password) {
    $stmt = $pdo->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}
