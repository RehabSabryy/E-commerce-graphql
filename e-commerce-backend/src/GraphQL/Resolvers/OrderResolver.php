<?php
namespace App\GraphQL\Resolvers;

use App\Core\Database;

class OrderResolver {
    protected $connection;

    public function __construct() {
        $this->connection = new Database('localhost', 'root', '', 'e-commerce-task');
    }

    public function createOrder($orderInput) {
        try {
            // Insert order
            $query = "INSERT INTO Orders (quantity, price, product_id) VALUES (:quantity, :price, :product_id)";
            $insert = $this->connection->prepare($query);
            $insert->bindParam(':quantity', $orderInput['quantity']);
            $insert->bindParam(':price', $orderInput['price']);
            $insert->bindParam(':product_id', $orderInput['product_id']);

            $insert->execute();

            // Insert product's attributes
            foreach ($orderInput['attributes'] as $attribute) {
                $query = "INSERT INTO Attributes_order (id, attribute_name, item_id, item_value, item_display_value, order_id) VALUES (:id, :attribute_name, :item_id, :item_value, :item_display_value, :order_id)";
                $insert = $this->connection->prepare($query);
                $insert->bindParam(':id', $attribute['id']);
                $insert->bindParam(':attribute_name', $attribute['attribute_name']);
                $insert->bindParam(':item_id', $attribute['item_id']);
                $insert->bindParam(':item_value', $attribute['item_value']);
                $insert->bindParam(':item_display_value', $attribute['item_display_value']);
                $insert->bindParam(':order_id', $attribute['order_id']);
                $insert->execute();
            }

            return [
                'quantity' => $orderInput['quantity'],
                'price' => $orderInput['price'],
                'product_id' => $orderInput['product_id'],
                'attributes' => $orderInput['attributes']
            ];
        } catch (\PDOException $e) {
            error_log('Failed to insert order: ' . $e->getMessage());
            return null;
        }
    }
}
