<?php
namespace App\Core;
class AttributeInsertion {
    protected $db;
    public function __construct(Database $db) {
        $this->db = $db;
    }
    public function insertAttributes(array $attributes , array $products) {
    }
}

