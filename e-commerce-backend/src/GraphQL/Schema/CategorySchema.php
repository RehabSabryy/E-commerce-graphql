<?php
namespace App\GraphQL\Schema;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use App\GraphQL\Resolvers\CategoryResolver;
class CategorySchema
{
    protected $categoryAttributes = [];
    protected $CategoryResolver;
    public function __construct(CategoryResolver $CategoryResolver)
    {
        $this->CategoryResolver = $CategoryResolver;
    }
    public function getCategoryType(): ObjectType
    {
        return new ObjectType([
            'name' => 'Category', 
            'fields' => [
                'name' => ['type' => Type::nonNull(Type::string())],
            ],
        ]);
    }
}
