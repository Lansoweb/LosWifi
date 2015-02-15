<?php
namespace LosUnifi;

return [
    'loswifi' => [
        'backend' => 'unifi'
    ],
    'service_manager' => [
        'factories' => [
            'LosUnifi\Options\ModuleOptions' => 'LosUnifi\Options\ModuleOptionsFactory',
            'LosUnifi\Service\Client' => 'LosUnifi\Service\ClientFactory'
        ],
        'aliases' => [
            'losunifi.options' => 'LosUnifi\Options\ModuleOptions',
            'losunifi.client' => 'LosUnifi\Service\Client'
        ]
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                ]
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
];
