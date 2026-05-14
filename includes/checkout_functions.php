<?php
// includes/checkout_functions.php

function calculateCartTotal(array $cart_items): float {
    $total = 0;
    foreach ($cart_items as $item) {
        $price = isset($item['price']) ? (float)$item['price'] : 0;
        $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
        $total += $price * $quantity;
    }
    return $total;
}

function validateShippingInfo(string $name, string $phone, string $address): array {
    $errors = [];
    if (trim($name) === '') {
        $errors[] = 'Vui lòng nhập họ tên người nhận';
    }
    if (trim($phone) === '') {
        $errors[] = 'Vui lòng nhập số điện thoại';
    }
    if (trim($address) === '') {
        $errors[] = 'Vui lòng nhập địa chỉ giao hàng';
    }
    return $errors;
}

function getCheckedProductIds(array $checkout_items): array {
    $ids = [];
    foreach ($checkout_items as $item) {
        if (isset($item['id'])) {
            $ids[] = (int)$item['id'];
        }
    }
    return $ids;
}

function removeCheckedCartItems(array $cart, array $checked_product_ids): array {
    return array_values(array_filter($cart, function($item) use ($checked_product_ids) {
        return !in_array((int)($item['id'] ?? 0), $checked_product_ids, true);
    }));
}
