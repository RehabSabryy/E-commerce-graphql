<?php
namespace App\Queries;

$query = "CREATE TABLE IF NOT EXISTS Prices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  amount FLOAT NOT NULL,
  label VARCHAR(255) NOT NULL,
  symbol VARCHAR(255) NOT NULL,
  product_id VARCHAR(255) NOT NULL,
  FOREIGN KEY (product_id) REFERENCES Products(id)
)";
$db->executeQuery($query);