<?php
/**
 * 系统配置
 */
return [
    'debug' => false, // 建议开发过程中开启
    'session' => [
        'client' => 'file',

        'config' => [
            'savePath' => SF_WORK_PATH.'storage/sessions',
            'host' => '127.0.0.1',
            'port' => 6379,
            'auth' => null,
            'timeout' => 5,
            'prefix' => 'sf:'
        ],

        'lifetime' => 7200
    ]
];