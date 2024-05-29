<?php
namespace App\Queries;
$query = "CREATE TABLE IF NOT EXISTS Attribute_items (
  itemId INT AUTO_INCREMENT,
  id VARCHAR(255) NOT NULL,
  displayValue VARCHAR(255) NOT NULL,
  value VARCHAR(255) NOT NULL,
  attribute_id VARCHAR(255) NOT NULL,
  product_id VARCHAR(255) NOT NULL,
  primary key (itemId, product_id, attribute_id),
  FOREIGN KEY (product_id) REFERENCES Products(id),
  FOREIGN KEY (attribute_id) REFERENCES Attributes(id)
)";
$db->executeQuery($query);
