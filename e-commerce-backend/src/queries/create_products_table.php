<?php
  namespace App\Queries;
 
$query = "CREATE TABLE IF NOT EXISTS Products (
  id VARCHAR(255) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  inStock BOOLEAN NOT NULL,
  description TEXT NOT NULL,
  brand VARCHAR(255),
  category VARCHAR(255),
  FOREIGN KEY (category) REFERENCES Categories(name)
)";
$db->executeQuery($query);
