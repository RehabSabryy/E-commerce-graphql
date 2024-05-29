<?php
namespace App\Core;

class CategoryInsertion {
    protected $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }
    protected function logMessage($message) {
        error_log($message);
    }
    public function insertCategories(array $categories) {
        try {
            foreach ($categories as $category) {
                $categoryName = $category['name'];
                $query = "SELECT name FROM Categories WHERE name = :name";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':name', $categoryName);
                $stmt->execute();
                $existingCategory = $stmt->fetch();
    
                if (!$existingCategory) {
                    $query = "INSERT INTO Categories (name) VALUES (:name)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':name', $categoryName);
                    $stmt->execute();
                }
            }
        } catch (\PDOException $e) {
            $this->logMessage("Category Insertion Error: " . $e->getMessage());
        }
    }
}