<?php

namespace App\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Resolvers\OrderResolver;
use App\GraphQL\Schema\OrderSchema;

class InsertOrders extends ObjectType {
    public function __construct() {
        $config = [
            'name' => 'Mutation',
            'fields' => [
                'createOrder' => [
                    'type' => OrderSchema::getOrderType(),
                    'args' => [
                        'order' => ['type' => OrderSchema::getOrderInputType()],
                    ],
                    'resolve' => function($root, $args) {
                        $orderResolver = new OrderResolver();
                        return $orderResolver->createOrder($args['order']);
                    }
                                    ]
            ]
        ];
        parent::__construct($config);
    }
}
