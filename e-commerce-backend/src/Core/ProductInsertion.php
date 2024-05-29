<?php
namespace App\Core;
include_once 'Database.php';
use App\Core\Database;

class ProductInsertion {
    protected $db;
    public function __construct(Database $db) {
        $this->db = $db;
    }
    protected function logMessage($message) {
        error_log($message);
    }
    public function insertProducts(array $products) {
        try {
            $this->db->beginTransaction();

            foreach ($products as $product) {
                $this->insertProductDetails($product);
                $this->insertProductGallery($product);
                $this->insertProductPrices($product);
                $this->insertProductAttributes($product);
            
                foreach($product['attributes'] as $attribute) {
                    $this->insertAttributesItems($attribute, $product);
                }
        }
            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollBack();
            $this->logMessage("Product Insertion Error: " . $e->getMessage());
        }
    }
    protected function insertProductDetails(array $product) {
        $productId = $product['id'];

        $query = "INSERT INTO Products (id, name, inStock, description, brand, category) 
                  VALUES (:id, :name, :inStock, :description, :brand, :category)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $productId);
        $stmt->bindParam(':name', $product['name']);
        $stmt->bindParam(':inStock', $product['inStock'], \PDO::PARAM_BOOL);
        $stmt->bindParam(':description', $product['description']);
        $stmt->bindParam(':brand', $product['brand']);
        $stmt->bindParam(':category', $product['category']);
        
        try {
            $stmt->execute();
        } catch (\PDOException $e) {
            // Check if the error is due to a duplicate entry
            if ($e->errorInfo[1] === 1062) { // Error code for duplicate entry
                // Do nothing if product already exists
                return;
            } else {
                // Rethrow the exception if it's a different error
                throw $e;
            }
        }
    }
    protected function insertProductGallery(array $product) {
        $query = "INSERT INTO Products_gallery (id, gallery) VALUES (:id, :gallery)";
        $stmt = $this->db->prepare($query);

        foreach ($product['gallery'] as $image) {
            $checkQuery = "SELECT COUNT(*) FROM Products_gallery WHERE id = :id AND gallery = :gallery";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':id', $product['id']);
            $checkStmt->bindParam(':gallery', $image);
            $checkStmt->execute();
            $rowCount = $checkStmt->fetchColumn();

            if ($rowCount == 0) {
                $stmt->bindParam(':id', $product['id']);
                $stmt->bindParam(':gallery', $image);
                $stmt->execute();
            }
        }
    }
    protected function insertProductPrices(array $product) {
        foreach ($product['prices'] as $price) {
            // check if the price already exists
            $checkQuery = "SELECT COUNT(*) FROM Prices WHERE product_id = :productId";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':productId', $product['id']);
            $checkStmt->execute();
            $rowCount = $checkStmt->fetchColumn();
            // if the price does not exist, insert it
            if ($rowCount == 0) {
                $query = "INSERT INTO Prices ( amount, label, symbol, product_id)
                          VALUES ( :amount, :label, :symbol, :productId)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':amount', $price['amount']);
                $stmt->bindParam(':label', $price['currency']['label']);
                $stmt->bindParam(':symbol', $price['currency']['symbol']);
                $stmt->bindParam(':productId', $product['id']);
                $stmt->execute();       
            }
            }
    }
    protected function insertProductAttributes(array $product) {
        // prevent duplicate attributes
        foreach($product['attributes'] as $attribute) {
            // Check if the attribute already exists for the product
            $checkQuery = "SELECT COUNT(*) FROM Attributes WHERE id = :attributeId AND product_id = :productId";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':attributeId', $attribute['id']);
            $checkStmt->bindParam(':productId', $product['id']);
            $checkStmt->execute();
            $rowCount = $checkStmt->fetchColumn();
    
            // If the attribute does not exist, insert it
            if ($rowCount == 0) {
                $query = "INSERT INTO Attributes (id, name, type, product_id)
                          VALUES (:attributeId, :attributeName, :attributeType, :productId)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':attributeId', $attribute['id']);
                $stmt->bindParam(':attributeName', $attribute['name']);
                $stmt->bindParam(':attributeType', $attribute['type']);
                $stmt->bindParam(':productId', $product['id']);
                $stmt->execute();
            }
        }
    }

    protected function insertAttributesItems(array $attribute, array $product) {
        foreach ($attribute['items'] as $item) {
            // check if the item already exists
            $checkQuery = "SELECT COUNT(*) FROM Attribute_items WHERE id = :itemId AND attribute_id = :attributeId AND product_id = :productId";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':itemId', $item['id']);
            $checkStmt->bindParam(':attributeId', $attribute['id']);
            $checkStmt->bindParam(':productId', $product['id']);
            $checkStmt->execute();
            $rowCount = $checkStmt->fetchColumn();
    
            // if the item does not exist, insert it
            if ($rowCount == 0) {
                $query = "INSERT INTO Attribute_items (id, displayValue, value, attribute_id, product_id)
                          VALUES (:itemId, :itemDisplayValue, :itemValue, :attributeId, :productId)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':itemId', $item['id']);
                $stmt->bindParam(':itemDisplayValue', $item['displayValue']);
                $stmt->bindParam(':itemValue', $item['value']);
                $stmt->bindParam(':attributeId', $attribute['id']); 
                $stmt->bindParam(':productId', $product['id']);
                $stmt->execute();
            }
        }
    }
    
}
