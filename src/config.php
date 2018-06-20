<?php

return [

    // websocket地址和端口
    'port'     => '0.0.0.0:2346',
    // Gateway通讯地址
    'register' => '127.0.0.1:1236',
    // 事件处理类
    'handler'  => \App\Workerman\Events::class,
    // 开启WSS模式
    'wss'      => false,
    // WSS模式必须的证书
    'ssl'      => [
        'local_cert'  => '/yourSSLcert.pem',
        'local_pk'    => '/yourSSLcert.key',
        'verify_peer' => false,
    ],
    // 服务器CPU数量
    'cpu'      => 1,
    // 心跳包内容
    'heart'    => '{"type":"@heart@"}',
];
