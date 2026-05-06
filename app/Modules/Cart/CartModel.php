<?php

namespace App\Modules\Cart;

use App\Core\Model;

class CartModel extends Model
{
    public function getProductById($id)
    {
        $stmt = $this->db->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
