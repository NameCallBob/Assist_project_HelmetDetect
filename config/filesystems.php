<?php

// 參考文獻:https://www.youtube.com/watch?v=m9CSLR8EGzM

return [

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        // 其他磁碟配置...
    'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
    ],

    // 其他配置...

];