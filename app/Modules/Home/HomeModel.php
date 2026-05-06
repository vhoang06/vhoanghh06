<?php

namespace App\Modules\Home;

use App\Core\Model;

class HomeModel extends Model
{
    public function getCategories()
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll();
    }

    public function getLatestProductsByCategory($categoryId, $limit = 4)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, b.name AS brand_name 
            FROM products p 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE p.category_id = ? 
            ORDER BY p.id DESC 
            LIMIT ?
        ");
        $stmt->execute([$categoryId, $limit]);
        return $stmt->fetchAll();
    }
}
