<?php
namespace App\GraphQL\Resolvers;

use App\Core\Database;

class CategoryResolver {
  
  public function resolveAllCategories()
  {
      try {
          $connection = new Database('localhost', 'root', '', 'e-commerce-task');
          $query = $connection->prepare("SELECT name FROM Categories");
          $query->execute();
          $categories = $query->fetchAll(\PDO::FETCH_ASSOC);
          return $categories;
      } catch (\PDOException $e) {
          error_log('Failed to fetch categories: ' . $e->getMessage());
          (error_reporting(E_ALL));
          ini_set('display_errors', '1');
          return [];
      }
    }  
}
$category = new CategoryResolver();
$category->resolveAllCategories();
