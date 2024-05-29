<?php
namespace App\Queries;
$query = "CREATE TABLE IF NOT EXISTS Products_gallery (
  id VARCHAR(255),
  gallery VARCHAR(255),
  PRIMARY KEY(id,gallery),
  FOREIGN KEY (id) REFERENCES Products(id)
)";
$db->executeQuery($query);