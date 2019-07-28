<?php

return [
    /*
    'redis' => [
        'scheme'   => 'tcp',
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'password' => null,
        'database' => 0
    ],
    */
    'redis' => getenv('OPENREDIS_URL'),
    
    'api_key_randomizer' => 'e48933c4-c3d1-4ddb-9471-c11470c6e25e',
    
    'players' => [
        'Casey' => 0,
        'Austin' => 0,
        'Tony' => 0,
        'Raphael' => 0,
        'Robin' => 0,
        'Matthew' => 0,
        'Jessie' => 0,
        'Aaron' => 0,
        'Jake' => 0,
        'Frank' => 0
    ],
    
    'player_update_max_value' => 10,
    
    'channel_pubsub' => 'channel_pubsub',
];