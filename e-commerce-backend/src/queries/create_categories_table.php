<?php
namespace App\Queries;

$query = "CREATE TABLE IF NOT EXISTS Categories (
 name VARCHAR(255) PRIMARY KEY
)";
$db->executeQuery($query);