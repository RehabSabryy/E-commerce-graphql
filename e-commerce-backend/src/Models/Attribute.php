<?php
namespace App\Models;
abstract class Attribute {
   protected $id;
   protected $name;
   protected $type;
   protected $productId;
   public function __construct($id , $name, $type , $productId) {
       $this->id = $id;
       $this->name = $name;
       $this->type = $type;
       $this->productId = $productId;
    }
   abstract public function getAttributeItems();
}
class AttributeItems extends Attribute {
    protected $itemId;
    protected $displayValue;
    protected $value;
    public function __construct($id, $name, $type, $productId, $itemId, $displayValue, $value) {
        parent::__construct($id, $name, $type, $productId);
        $this->itemId = $itemId;
        $this->displayValue = $displayValue;
        $this->value = $value;
    }
    public function getAttributeItems(){
        return $this->name;
    }
}