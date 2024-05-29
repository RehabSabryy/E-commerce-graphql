<?php
namespace App\GraphQL\Schema;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use App\Models\Attribute;
use App\GraphQL\Resolvers\AttributeResolver;
class AttributeSchema extends ObjectType {

 public function __construct() {
    $config = [
      'name' => 'Attributes',
          'fields' => [
              'id' => ['type' => Type::nonNull(Type::string())],
              'name' => ['type' => Type::nonNull(Type::string())],
              'type' => ['type' => Type::nonNull(Type::string())],
              'items' => [
                  'type' => Type::listOf(
                      new ObjectType([
                          'name' => 'Item',
                          'fields' => [
                              'id' => ['type' => Type::nonNull(Type::string())],
                              'displayValue' => ['type' => Type::nonNull(Type::string())],
                              'value' => ['type' => Type::nonNull(Type::string())],
                          ],
                      ])
                  ),
              ],
          ],
      ];
 }
  public function getAttributeType(): ObjectType
  {
      return new ObjectType([
          'name' => 'Attributes',
          'fields' => [
              'id' => ['type' => Type::nonNull(Type::string())],
              'name' => ['type' => Type::nonNull(Type::string())],
              'type' => ['type' => Type::nonNull(Type::string())],
              'items' => [
                  'type' => Type::listOf(
                      new ObjectType([
                          'name' => 'Item',
                          'fields' => [
                              'id' => ['type' => Type::nonNull(Type::string())],
                              'displayValue' => ['type' => Type::nonNull(Type::string())],
                              'value' => ['type' => Type::nonNull(Type::string())],
                          ],
                      ])
                  ),
              ],
          ],
      ]);
  }
}