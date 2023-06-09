<?php

// 服务接口
$services = [
    'UserService' => \App\JsonRpc\Interface\UserServiceInterface::class,
];

return [
    'consumers' => value(function () use($services) {
        $consumers = [];
        foreach ($services as $name => $interface) {
            $consumers[] = [
                'name'          => $name,
                'service'       => $interface,
                'load_balancer' => 'random',
                'registry'      => [
                    'protocol' => 'consul',
                    'address'  => 'http://192.168.31.90:8500'
                ]
            ];
        }
        return $consumers;
    }),
];

//return [
//    'consumers' => [
//        [
//            // 对应消费者类的 $serviceName
//            'name'    => 'UserService',
//            // 服务接口名，可选，默认值等于 name 配置的值，如果 name 直接定义为接口类则可忽略此行配置，
//            // 如 name 为字符串则需要配置 service 对应到接口类
//            'service' => \App\JsonRpc\Interface\UserServiceInterface::class,
//            // 直接对指定的节点进行消费，通过下面的 nodes 参数来配置服务提供者的节点信息
//            'nodes' => [
//                ['host' => '192.168.31.90', 'port' => 9600],
//            ],
//        ]
//    ],
//];