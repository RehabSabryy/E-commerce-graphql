<?php
namespace App\Queries;

$query= "CREATE TABLE IF NOT EXISTS Attributes (
  id VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  type VARCHAR(255) NOT NULL,
  product_id VARCHAR(255) NOT NULL,
  PRIMARY KEY (id, product_id),
  FOREIGN KEY (product_id) REFERENCES Products(id)
  )";
$db->executeQuery($query);