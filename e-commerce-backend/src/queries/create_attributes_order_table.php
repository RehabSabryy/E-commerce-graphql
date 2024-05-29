<?php

namespace App\Queries;
$query= "CREATE TABLE IF NOT EXISTS Attributes_order (   
    id VARCHAR(255) PRIMARY KEY,
    attribute_name VARCHAR(255) NOT NULL,
    item_id VARCHAR(255) NOT NULL,
    item_value VARCHAR(255) NOT NULL,
    item_display_value VARCHAR(255) NOT NULL,
    order_id int NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(id)
    )";
$db->executeQuery($query);