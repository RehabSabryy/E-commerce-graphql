<?php
namespace App\Core;
include_once 'Database.php';
include_once 'ProductInsertion.php';
include_once 'CategoryInsertion.php';
include_once 'AttributeInsertion.php';

use App\Core\CategoryInsertion;
use App\Core\ProductInsertion;
use App\Core\AttributeInsertion;
// class responsible for coordinating the insertion of data into the database
 class DataInsertion {
    public $db;
    public function __construct(Database $db) {
        $this->db = $db;
    }
    public function insertData(array $data) {
        $this->insertCategories($data['data']['categories']);
        $this->insertProducts($data['data']['products']);
        foreach ($data['data']['products'] as $product) {
            if (isset($product['attributes']) && !empty($product['attributes'])) {
                $this->insertAttributes($product['attributes'], $data['data']['products']);
            }
        }  
      }
    public function insertCategories(array $categories) {
        $categoryInsertion = new CategoryInsertion($this->db);
        $categoryInsertion->insertCategories($categories);
    }
    public function insertProducts(array $products) {
        $productInsertion = new ProductInsertion($this->db);
        $productInsertion->insertProducts($products);
    }
    public function insertAttributes(array $attributes , array $products) {
        // $attributeInsertion = new AttributeInsertion($this->db);
        // $attributeInsertion->insertAttributes($attributes, $products);
    }
}