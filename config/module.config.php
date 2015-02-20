<?php
namespace LosWifi;

return [
    'loswifi' => [
        'controllers' => []
    ],
    'service_manager' => [
        'factories' => [
            'LosWifi\Options\ModuleOptions' => 'LosWifi\Options\ModuleOptionsFactory',
            'LosWifi\Service\Client' => 'LosWifi\Service\ClientFactory'
        ],
        'aliases' => [
            'loswifi.options' => 'LosWifi\Options\ModuleOptions',
            'loswifi.client' => 'LosWifi\Service\Client'
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
