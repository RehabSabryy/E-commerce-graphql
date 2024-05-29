<?php
require_once __DIR__  . '/src/Controllers/GraphQLController.php';
use App\Controllers\GraphQLController;
// Route incoming requests
switch ($_SERVER['REQUEST_URI']) {
    case '/graphql':
        // Handle GraphQL requests
        GraphQLController::handle();
        break;
    default:
        break;
}