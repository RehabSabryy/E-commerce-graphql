<?php

require_once __DIR__ . '/bootstrap.php';

use App\Core\DataInsertion;

// Read JSON file
$json = file_get_contents('data.json');
$data = json_decode($json, true);

// Insert data
$dataInsertion = new DataInsertion($db);
$dataInsertion->insertData($data);
