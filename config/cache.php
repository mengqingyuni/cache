<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => [
        "driver" => "cache.driver",
        "type"   => "file"
    ],
    // 缓存连接方式配置

    'file' => [
        // 驱动方式
        'type'       => 'File',
        // 缓存保存目录
        'path'       => './data/file',
        // 缓存前缀
        'prefix'     => '',
        // 缓存有效期 0表示永久缓存
        'expire'     => 0,
        // 缓存标签前缀
        'tag_prefix' => 'tag:',
        // 序列化机制 例如 ['serialize', 'unserialize']
        'serialize'  => [],
    ],
    // 更多的缓存连接
    'redis' => [
        // 驱动方式
        'type'  => 'redis',
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '123456',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 0,
        'persistent' => false,
        'prefix'     => '',
        'tag_prefix' => 'tag:',
        'serialize'  => [],
    ],

];