<?php

namespace App\Modules\Product;

use App\Core\Model;

class ProductModel extends Model
{
    public function getProducts($search = '', $category_id = 0, $brand_id = 0)
    {
        $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND p.name LIKE ?";
            $params[] = "%$search%";
        }

        if ($category_id > 0) {
            $sql .= " AND p.category_id = ?";
            $params[] = $category_id;
        }

        if ($brand_id > 0) {
            $sql .= " AND p.brand_id = ?";
            $params[] = $brand_id;
        }

        $sql .= " ORDER BY p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getProductById($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name, b.name AS brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getCategories()
    {
        return $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    }

    public function getBrands()
    {
        return $this->db->query("SELECT * FROM brands ORDER BY name")->fetchAll();
    }

    public function getRelatedProducts($category_id, $current_product_id, $limit = 4)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM products 
            WHERE category_id = ? AND id != ? 
            LIMIT ?
        ");
        $stmt->bindValue(1, $category_id, \PDO::PARAM_INT);
        $stmt->bindValue(2, $current_product_id, \PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
