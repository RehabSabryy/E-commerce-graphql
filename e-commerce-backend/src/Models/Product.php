<?php
namespace App\Models;
abstract class Product {

    protected $id;
    protected $name;
    protected $inStock;
    protected $gallery;
    protected $description;
    protected $category;
    protected $brand;

    public function __construct($id, $name, $inStock, $gallery, $description, $category,$brand) {
       $this->id = $id;
       $this->name = $name;
       $this->inStock = $inStock;
       $this->gallery = $gallery;
       $this->description = $description;
       $this->category = $category;
       $this->brand = $brand;

    } 
    abstract public function getProductDetails();
}
class productDetails extends Product {

    
    public function __construct($id, $name, $inStock, $gallery, $description, $category, $brand, $amount , $label , $symbol) {
        parent::__construct($id, $name, $inStock, $gallery, $description, $category, $brand);
        $this->amount = $amount;
        $this->label = $label;
        $this->symbol = $symbol;
    }
    public function getProductDetails(){
        return $this->name;
    }
}