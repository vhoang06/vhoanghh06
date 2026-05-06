<?php

namespace App\Modules\Order;

use App\Core\Model;

class OrderModel extends Model
{
    public function getUserOrders($userId)
    {
        $stmt = $this->db->prepare("
            SELECT o.*, 
            (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count 
            FROM orders o 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function createOrder($data, $cart)
    {
        $this->db->beginTransaction();
        try {
            // Insert Order
            $stmt = $this->db->prepare("
                INSERT INTO orders (user_id, total_amount, shipping_name, shipping_phone, shipping_address, note, payment_method) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['user_id'],
                $data['total_amount'],
                $data['shipping_name'],
                $data['shipping_phone'],
                $data['shipping_address'],
                $data['note'],
                $data['payment_method']
            ]);
            $orderId = $this->db->lastInsertId();

            // Insert Items & Update Stock
            $stmt_item = $this->db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt_update_stock = $this->db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

            foreach ($cart as $id => $item) {
                $stmt_item->execute([$orderId, $id, $item['quantity'], $item['price']]);
                $stmt_update_stock->execute([$item['quantity'], $id]);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
