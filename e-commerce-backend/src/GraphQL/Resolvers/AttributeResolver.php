<?php

namespace App\GraphQL\Resolvers;

use App\Core\Database; 
use PDO;

class AttributeResolver
{
    public function resolveProductAttributes($productId)
    {
        try {
            $connection = new Database('localhost', 'root', '', 'e-commerce-task');
            $query = "SELECT a.id, a.name, a.type, ai.id AS item_id, ai.displayValue, ai.value
                FROM Attributes a
                LEFT JOIN Attribute_items ai ON a.id = ai.attribute_id 
                where a.product_id = ai.product_id AND a.product_id = :productId
            ";
            $attributesRes = $connection->prepare($query);
            $attributesRes->execute(['productId' => $productId]);
            $attributeData = $attributesRes->fetchAll(PDO::FETCH_ASSOC);
    
            $attributes = [];
            foreach ($attributeData as $row) {
                $attributeId = $row['id'];
                $attribute = isset($attributes[$attributeId]) ? $attributes[$attributeId] : [
                    'id' => $attributeId,
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'items' => [], 
                ];
                    // Check if the attribute has items and add only the unique items
                if ($row['item_id']) {
                    $attribute['items'][] = [
                        'id' => $row['item_id'],
                        'displayValue' => $row['displayValue'],
                        'value' => $row['value'],
                    ];
                }
    
                $attributes[$attributeId] = $attribute;
            }
    
            return array_values($attributes);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch attributes: ' . $e->getMessage());
        }
    }
}
