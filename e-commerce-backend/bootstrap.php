<?php 
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once 'src/Controllers/GraphQLController.php';
require_once 'src/GraphQL/Resolvers/CategoryResolver.php';
require_once 'src/GraphQL/Resolvers/ProductResolver.php';
require_once 'src/GraphQL/Resolvers/AttributeResolver.php';
require_once 'src/Core/Database.php';
require_once 'src/Models/Category.php';
require_once 'src/Models/Product.php';
require_once 'src/Models/Attribute.php';
require_once 'src/Core/CategoryInsertion.php';
require_once 'src/Core/ProductInsertion.php';
require_once 'src/Core/AttributeInsertion.php';
require_once 'src/Core/DataInsertion.php';

use App\Core\Database;
use App\Core\DataInsertion;


$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
require_once 'src/Queries/create_categories_table.php';
require_once 'src/Queries/create_products_table.php';
require_once 'src/Queries/create_prices_table.php';
require_once 'src/Queries/create_attributes_table.php';
require_once 'src/Queries/create_attribute_items_table.php';
require_once 'src/Queries/create_products_gallery_table.php';
require_once 'src/Queries/create_orders_table.php';
require_once 'src/Queries/create_attributes_order_table.php';


