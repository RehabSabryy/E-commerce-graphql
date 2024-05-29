<?php

namespace App\GraphQL\Schema;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;

class OrderSchema {
    public static function getOrderType(): ObjectType {
        return new ObjectType([
            'name' => 'Order',
            'fields' => [
                'quantity' => ['type' => Type::nonNull(Type::int())],
                'price' => ['type' => Type::nonNull(Type::float())],
                'product_id' => ['type' => Type::nonNull(Type::string())],
                'attributes' => [
                    'type' => Type::listOf(self::getAttributeType())
                ],
            ]
        ]);
    }

    public static function getAttributeType(): ObjectType {
        return new ObjectType([
            'name' => 'Attribute',
            'fields' => [
                'id' => ['type' => Type::string()],
                'attribute_name' => ['type' => Type::string()],
                'item_id' => ['type' => Type::string()],
                'item_value' => ['type' => Type::string()],
                'item_display_value' => ['type' => Type::string()],
                'order_id' => ['type' => Type::int()],
            ]
        ]);
    }

    public static function getOrderInputType(): InputObjectType {
        return new InputObjectType([
            'name' => 'OrderInput',
            'fields' => [
                'quantity' => ['type' => Type::nonNull(Type::int())],
                'price' => ['type' => Type::nonNull(Type::float())],
                'product_id' => ['type' => Type::nonNull(Type::string())],
                'attributes' => [
                    'type' => Type::listOf(self::getAttributeInputType())
                ],
            ]
        ]);
    }

    public static function getAttributeInputType(): InputObjectType {
        return new InputObjectType([
            'name' => 'AttributeInput',
            'fields' => [
                'id' => ['type' => Type::string()],
                'attribute_name' => ['type' => Type::string()],
                'item_id' => ['type' => Type::string()],
                'item_value' => ['type' => Type::string()],
                'item_display_value' => ['type' => Type::string()],
                'order_id' => ['type' => Type::int()],
            ]
        ]);
    }
}
