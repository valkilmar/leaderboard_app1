<?php

return [
    
    'redis' => getenv('REDIS_URL'),

    'storage_key_leaderboard' => 'leaderboard',

    'storage_key_app_status' => 'app_status',
    
    'api_key_randomizer' => [
        //'e48933c4-c3d1-4ddb-9471-c11470c6e25e',
        '793d0d05-4f89-4c29-8b72-472b655a67a3',
        '7d21012e-373c-4323-9104-1fea8dbd79dc'
    ],

    'player_update_min_value' => -10, // Min update value the player's score per poll
    
    'player_update_max_value' => 25, // Max update value the player's score per poll

    'player_update_min_count' => 1, // Min count of players to be updated in a single poll

    'player_update_max_count' => 10, // Max count of players to be updated in a single poll

    'min_polling_delay' => 2, // In seconds

    'max_polling_delay' => 4, // In seconds

    'channel_leaderboard' => 'channel_leaderboard', // Channel name to which Storage should emit notifications on data change

    'names' => 'Sam|Addison|Alex|Anderson|Armstrong|Baker|Bell|Bruce|Bryant|Brynn|Carol|Clark|Cooke|Dane|Dawson|Denny|Drew|Elliott|Evans|Fleming|Fletcher|Foreman|Gallegos|Gibson|Gilbert|Glenn|Griffin|Harley|Hodge|Jaden|Jaime|Justice|Kelly|Lane|Lesley|Lewis|Maddox|Mason|Mel|Mitchell|Montoya|Murray|Nelson|Powers|Ramirez|Ray|Robin|Robinson|Ryan|Schroeder|Shay|Sidney|Skye|Steff|Taylor|Terry|Thomson|Tyler|Val|Williams|Casey|Austin|Tony|Raphael|Robin|Matthew|Jessie|Aaron|Jake|Frank',
];