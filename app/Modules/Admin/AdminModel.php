<?php

namespace App\Modules\Admin;

use App\Core\Model;

class AdminModel extends Model
{
    public function getStats()
    {
        $stats = [];
        
        $stats['total_products'] = $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $stats['total_orders'] = $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $stats['total_users'] = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['total_revenue'] = $this->db->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?: 0;
        
        return $stats;
    }

    public function getRecentOrders($limit = 5)
    {
        $sql = "SELECT o.*, u.username as customer_name 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC 
                LIMIT $limit";
        return $this->db->query($sql)->fetchAll();
    }

    public function getAllProducts()
    {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                ORDER BY p.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getAllOrders()
    {
        $sql = "SELECT o.*, u.username as customer_name 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function updateOrderStatus($orderId, $status)
    {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }

    public function deleteProduct($id)
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function saveProduct($data)
    {
        if (isset($data['id']) && $data['id'] > 0) {
            // Update
            $sql = "UPDATE products SET 
                    name = ?, category_id = ?, brand_id = ?, 
                    price = ?, stock = ?, 
                    description = ?, image = ? 
                    WHERE id = ?";
            $params = [
                $data['name'], $data['category_id'], $data['brand_id'],
                $data['price'], $data['stock'],
                $data['description'], $data['image'], $data['id']
            ];
        } else {
            // Insert
            $sql = "INSERT INTO products (name, category_id, brand_id, price, stock, description, image) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $data['name'], $data['category_id'], $data['brand_id'],
                $data['price'], $data['stock'],
                $data['description'], $data['image']
            ];
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getOrderById($id)
    {
        $stmt = $this->db->prepare("
            SELECT o.*, u.username as customer_name 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getOrderItems($orderId)
    {
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name as product_name, p.image 
            FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function updateOrderShipping($orderId, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE orders SET 
            shipping_name = ?, shipping_phone = ?, 
            shipping_address = ?, note = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['shipping_name'], $data['shipping_phone'], 
            $data['shipping_address'], $data['note'], $orderId
        ]);
    }

    public function createOrder($data, $items)
    {
        try {
            $this->db->beginTransaction();

            // 1. Kiểm tra tồn kho trước khi tạo
            foreach ($items as $item) {
                $stmt = $this->db->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
                $stmt->execute([$item['product_id']]);
                $currentStock = $stmt->fetchColumn();

                if ($currentStock < $item['quantity']) {
                    throw new \Exception("Sản phẩm ID #{$item['product_id']} không đủ hàng trong kho.");
                }
            }

            // 2. Chèn thông tin Đơn hàng
            $stmt = $this->db->prepare("
                INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_name, shipping_phone, shipping_address, note) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['user_id'], $data['total_amount'], $data['status'], 
                $data['payment_method'], $data['shipping_name'], 
                $data['shipping_phone'], $data['shipping_address'], $data['note']
            ]);
            
            $orderId = $this->db->lastInsertId();

            // 3. Chèn chi tiết đơn hàng & Trừ kho
            $itemStmt = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (?, ?, ?, ?)
            ");
            $updateStockStmt = $this->db->prepare("
                UPDATE products SET stock = stock - ? WHERE id = ?
            ");

            foreach ($items as $item) {
                // Lưu chi tiết
                $itemStmt->execute([
                    $orderId, $item['product_id'], $item['quantity'], $item['price']
                ]);
                
                // Trừ kho
                $updateStockStmt->execute([$item['quantity'], $item['product_id']]);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            // Bạn có thể log lỗi ở đây: $e->getMessage()
            return false;
        }
    }

    public function getAllUsers()
    {
        return $this->db->query("SELECT id, username, email FROM users ORDER BY username ASC")->fetchAll();
    }
}
