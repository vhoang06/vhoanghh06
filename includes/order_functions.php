<?php
// includes/order_functions.php

function placeOrder(PDO $pdo, int $user_id, array $checkout_items, string $shipping_name, string $shipping_phone, string $shipping_address, string $note = ''): array {
    $errors = [];
    $total = 0;
    foreach ($checkout_items as $item) {
        $price = isset($item['price']) ? (float)$item['price'] : 0;
        $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
        $total += $price * $quantity;
    }

    if (empty($checkout_items)) {
        $errors[] = 'Không có sản phẩm trong đơn hàng';
        return ['success' => false, 'errors' => $errors];
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_name, shipping_phone, shipping_address, note, payment_method) VALUES (?, ?, ?, ?, ?, ?, 'cod')");
        $stmt->execute([$user_id, $total, $shipping_name, $shipping_phone, $shipping_address, $note]);
        $order_id = (int)$pdo->lastInsertId();

        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_get_stock = $pdo->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
        $stmt_update_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($checkout_items as $item) {
            $product_id = (int)($item['id'] ?? 0);
            $quantity = (int)($item['quantity'] ?? 0);
            $price = (float)($item['price'] ?? 0);

            $stmt_get_stock->execute([$product_id]);
            $current_stock = (int)$stmt_get_stock->fetchColumn();
            if ($current_stock < $quantity) {
                throw new Exception("Sản phẩm '{$item['name']}' không đủ tồn kho ({$current_stock} còn, yêu cầu {$quantity})");
            }

            $stmt_update_stock->execute([$quantity, $product_id]);
            $stmt_item->execute([$order_id, $product_id, $quantity, $price]);
        }

        $pdo->commit();
        return ['success' => true, 'order_id' => $order_id, 'total' => $total, 'errors' => []];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'errors' => ["Có lỗi xảy ra khi xử lý đơn hàng: " . $e->getMessage()]];
    }
}
