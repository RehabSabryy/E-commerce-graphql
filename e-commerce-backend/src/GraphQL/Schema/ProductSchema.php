<?php

namespace App\GraphQL\Schema;
include_once 'PriceSchema.php';
include_once 'AttributeSchema.php';
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Resolvers\ProductResolver;
use App\GraphQL\Resolvers\AttributeResolver;
use App\GraphQL\Schema\AttributeSchema;
use App\GraphQL\Schema\PriceSchema;
class ProductSchema extends ObjectType
{
    protected $productResolver;
    protected $attributeResolver;
    protected $attributeSchema;
    protected $priceSchema;

    public function __construct(ProductResolver $productResolver, AttributeResolver $attributeResolver)
    {
        $this->productResolver = $productResolver;
        $this->attributeResolver = $attributeResolver;
        $this->priceSchema = new PriceSchema();
        $this->attributeSchema = new AttributeSchema(); // Instantiate the AttributeSchema class here

        $config = [
            'name' => 'Products',
            'type' => Type::nonNull(Type::listOf($this->getProductType())),
            'args' => [
                'id' => Type::string(),
                'category' => Type::string(),
            ],
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
        ];

        parent::__construct($config);
    }

    public function getProductType(): ObjectType
    {
        return new ObjectType([
            'name' => 'Product',
            'fields' => [
                'id' => ['type' => Type::nonNull(Type::string())],
                'name' => ['type' => Type::nonNull(Type::string())],
                'inStock' => ['type' => Type::nonNull(Type::int())],
                'description' => ['type' => Type::nonNull(Type::string())],
                'category' => ['type' => Type::nonNull(Type::string())],
                'brand' => ['type' => Type::nonNull(Type::string())],
                'prices' => ['type' => Type::nonNull(Type::listOf($this->priceSchema->getPriceType()))],
                'gallery' => ['type' => Type::nonNull(Type::listOf(Type::string()))],
                'attributes' => ['type' => Type::listOf($this->attributeSchema->getAttributeType())], // Corrected line
            ],
        ]);
    }
}
