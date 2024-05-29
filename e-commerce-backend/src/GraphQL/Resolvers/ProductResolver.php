<?php

namespace App\GraphQL\Resolvers;

use App\Core\Database;

class ProductResolver
{
    protected $attributeResolver;
    protected $connection;

    public function __construct(AttributeResolver $attributeResolver)
    {
        $this->attributeResolver = $attributeResolver;
        $this->connection = new Database('localhost', 'root', '', 'e-commerce-task');
    }

    // Resolver method to fetch all products with prices
    public function resolveAllProducts()
    {
        try {
            $query = "SELECT p.id, p.name, p.inStock, p.description, p.brand, p.category, pr.amount, pr.label, pr.symbol, GROUP_CONCAT(g.gallery) AS gallery 
                        FROM Products p 
                        LEFT JOIN Prices pr ON p.id = pr.product_id 
                        LEFT JOIN Products_gallery g ON p.id = g.id
                        GROUP BY p.id";
            $productsRes = $this->connection->prepare($query);
            $productsRes->execute();
            $products = $productsRes->fetchAll(\PDO::FETCH_ASSOC);

            // Iterate over each product to construct prices field
            foreach ($products as &$product) {
                $this->processProduct($product);
            }

            return $products;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch products with prices: ' . $e->getMessage());
        }
    }

    public function resolveProductById($productId) {
        try {
            $query = "SELECT p.id, p.name, p.inStock, p.description, p.brand, p.category, pr.amount, pr.label, pr.symbol, GROUP_CONCAT(g.gallery) AS gallery 
                        FROM Products p 
                        LEFT JOIN Prices pr ON p.id = pr.product_id 
                        LEFT JOIN Products_gallery g ON p.id = g.id 
                        WHERE p.id = :id
                        GROUP BY p.id";
            $productsRes = $this->connection->prepare($query);
            $productsRes->bindParam(':id', $productId);
            $productsRes->execute();
            $product = $productsRes->fetch(\PDO::FETCH_ASSOC);

            if (!$product) {
                return []; // Return an empty array if no product is found
            }

            $this->processProduct($product);

            return [$product];
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch product by ID: ' . $e->getMessage());
        }
    }

    public function resolveProductsByCategory($category) {
        try {
            $query = "SELECT p.id, p.name, p.inStock, p.description, p.brand, p.category, pr.amount, pr.label, pr.symbol, GROUP_CONCAT(g.gallery) AS gallery 
                        FROM Products p 
                        LEFT JOIN Prices pr ON p.id = pr.product_id 
                        LEFT JOIN Products_gallery g ON p.id = g.id 
                        WHERE p.category = :category
                        GROUP BY p.id";
            $productsRes = $this->connection->prepare($query);
            $productsRes->bindParam(':category', $category);
            $productsRes->execute();
            $products = $productsRes->fetchAll(\PDO::FETCH_ASSOC);

            // Iterate over each product to construct prices field
            foreach ($products as &$product) {
                $this->processProduct($product);
            }

            return $products;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to fetch products by category: ' . $e->getMessage());
        }
    }

    protected function processProduct(&$product) {
        // Fetch attributes for the current product
        $product['attributes'] = $this->attributeResolver->resolveProductAttributes($product['id']);

        // Check if the product has price information
        if ($product['amount'] !== null && $product['label'] !== null && $product['symbol'] !== null) {
            // If price information exists, construct prices array
            $product['prices'] = [
                [
                    'amount' => (float)$product['amount'],
                    'currency' => [
                        'label' => $product['label'],
                        'symbol' => $product['symbol']
                    ]
                ]
            ];
        } else {
            $product['prices'] = null;
        }

        $product['gallery'] = explode(',', $product['gallery']);
    }
}
