<?php
namespace App\Models;

abstract class Category {
    protected $name;
    public function __construct($name) {
        $this->name = $name;        
    }
    abstract public function getCategoryName();
}
class categoryName extends Category {
    public function getCategoryName() {
        return $this->name;
    }
}
