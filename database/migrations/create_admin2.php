<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    $username = 'admin2';
    $password = '123';
    $email = 'admin2@example.com';
    $role = 'admin';
    $verified = 1;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if exists
    $check = $db->prepare("SELECT id FROM users WHERE username = ?");
    $check->execute([$username]);
    if ($check->fetch()) {
        echo "User '$username' already exists.\n";
        exit;
    }

    $stmt = $db->prepare("INSERT INTO users (username, password, email, role, email_verified) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$username, $hashedPassword, $email, $role, $verified]);

    if ($result) {
        echo "Admin account '$username' created successfully with password '$password'.\n";
    } else {
        echo "Failed to create admin account.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
