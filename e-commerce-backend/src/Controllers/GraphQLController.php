<?php

namespace App\Controllers;
require_once 'src/Core/Database.php';
require_once 'src/GraphQL/Resolvers/ProductResolver.php';
require_once 'src/GraphQL/Resolvers/CategoryResolver.php';
require_once 'src/GraphQL/Resolvers/AttributeResolver.php';
require_once 'src/GraphQL/Schema/ProductSchema.php';
require_once 'src/GraphQL/Schema/CategorySchema.php';
require_once 'src/GraphQL/Schema/AttributeSchema.php';
require_once 'src/GraphQL/Mutation/InsertOrders.php';
require_once 'src/GraphQL/Schema/OrderSchema.php';
require_once 'src/GraphQL/Resolvers/OrderResolver.php';   
use GraphQL\Type\Definition\ObjectType;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Resolvers\CategoryResolver;
use App\GraphQL\Schema\CategorySchema;
use App\GraphQL\Resolvers\ProductResolver;
use App\GraphQL\Schema\ProductSchema;
use App\GraphQL\Resolvers\AttributeResolver;
use App\GraphQL\Schema\AttributeSchema;
use App\GraphQL\Mutation\InsertOrders;

class GraphQLController {

    static public function handle() {
        try {

            $categoryResolver = new CategoryResolver();
            $categorySchema = new CategorySchema($categoryResolver);
            $attributeResolver = new AttributeResolver();
            $productResolver = new ProductResolver($attributeResolver);
            $attributeSchema = new AttributeSchema();
            $productSchema = new ProductSchema($productResolver, $attributeResolver);
            $insertOrders = new InsertOrders();
            $queryType = new ObjectType([
                    'name' => 'query',
                    'fields' => [
                        'Categories' => ['type' => Type::nonNull(Type::listOf($categorySchema->getCategoryType())),
                        'resolve' => function ($root, $args) use ($categoryResolver) {
                            return $categoryResolver->resolveAllCategories();
                                    }
                    ],

                    'Products' => ['type' => Type::nonNull(Type::listOf($productSchema->getProductType())),
                        'args' => ['id' => Type::string() , 
                                    'category' => Type::string()],   
                        'resolve' => function ($root, $args) use ($productResolver) {
                            if (isset($args['id'])) {
                              return $productResolver->resolveProductById($args['id']);
                            }
                            elseif (isset($args['category'])) {
                              return $productResolver->resolveProductsByCategory($args['category']);
                            }
                             else {
                              return $productResolver->resolveAllProducts();
                            }
                          },
                      ],
                    ],
                  ]);        
                  $mutationType = new InsertOrders();  
            // Create GraphQL schema configuration
               // Wrap queryType inside a closure
               $queryTypeCallable = function () use ($queryType) {
                return $queryType;
            };
                $mutationTypeCallable = function () use ($mutationType) {
                    return $mutationType;
                };
            // Create GraphQL schema configuration
            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryTypeCallable)
                    ->setMutation($mutationTypeCallable)
            ); 
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }     
            $input = json_decode($rawInput, true);
            if ($input === null) {
                throw new RuntimeException('Invalid JSON input');
            }      
            $query = $input['query'] ?? null;
            $variableValues = $input['variables'] ?? null;  
            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }
            header('Content-Type: application/json; charset=UTF-8');
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization");
            echo json_encode($output);
    }
}
$graphQLController = new GraphQLController();
$graphQLController->handle();