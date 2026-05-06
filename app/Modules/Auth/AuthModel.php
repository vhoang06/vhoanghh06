<?php

namespace App\Modules\Auth;

use App\Core\Model;

class AuthModel extends Model
{
    public function getUserByUsernameOrEmail($identifier)
    {
        $stmt = $this->db->prepare("SELECT id, username, email, password, role, email_verified FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$identifier, $identifier]);
        return $stmt->fetch();
    }

    public function updateLastLogin($userId)
    {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function register($username, $email, $password, $verifyToken)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password, role, email_verified, verify_token, verify_expires) 
            VALUES (?, ?, ?, 'user', 0, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))
        ");
        return $stmt->execute([$username, $email, $hashedPassword, $verifyToken]);
    }

    public function getUserByVerifyToken($email, $token)
    {
        $stmt = $this->db->prepare("
            SELECT id, username, email_verified 
            FROM users 
            WHERE email = ? AND verify_token = ? AND verify_expires > NOW()
        ");
        $stmt->execute([$email, $token]);
        return $stmt->fetch();
    }

    public function verifyEmail($userId)
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET email_verified = 1, verify_token = NULL, verify_expires = NULL 
            WHERE id = ?
        ");
        return $stmt->execute([$userId]);
    }

    public function setResetToken($email, $token)
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) 
            WHERE email = ?
        ");
        return $stmt->execute([$token, $email]);
    }

    public function getUserByResetToken($email, $token)
    {
        $stmt = $this->db->prepare("
            SELECT id, username 
            FROM users 
            WHERE email = ? AND reset_token = ? AND reset_expires > NOW()
        ");
        $stmt->execute([$email, $token]);
        return $stmt->fetch();
    }

    public function resetPassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("
            UPDATE users 
            SET password = ?, reset_token = NULL, reset_expires = NULL 
            WHERE id = ?
        ");
        return $stmt->execute([$hashedPassword, $userId]);
    }
}
