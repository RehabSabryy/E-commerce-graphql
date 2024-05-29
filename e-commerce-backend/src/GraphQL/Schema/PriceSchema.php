<?php
namespace App\GraphQL\Schema;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class PriceSchema extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Price',
            'fields' => [
                'amount' => ['type' => Type::nonNull(Type::float())],
                'currency' => ['type' => $this->getCurrencyType()],
            ],
        ];
        
    }
    public function getPriceType(): ObjectType
    {
        return new ObjectType([
            'name' => 'Price',
            'fields' => [
                'amount' => ['type' => Type::nonNull(Type::float())],
                'currency' => ['type' => $this->getCurrencyType()],
            ],
        ]);
    }

    public function getCurrencyType(): ObjectType
    {
        return new ObjectType([
            'name' => 'Currency',
            'fields' => [
                'label' => ['type' => Type::nonNull(Type::string())],
                'symbol' => ['type' => Type::nonNull(Type::string())],
            ],
        ]);
    }
}
