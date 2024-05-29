<?php
namespace App\Queries;

$query= "CREATE TABLE IF NOT EXISTS Orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quantity INT NOT NULL,
    price FLOAT NOT NULL,
    product_id varchar(255) NOT NULL,   
    FOREIGN KEY (product_id) REFERENCES Products(id)
    )";
$db->executeQuery($query);