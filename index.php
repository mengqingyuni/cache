<?php
namespace cache;
use config;
require __DIR__ . '/vendor/autoload.php';

//目录配置文件路径
define('CONFIG_PATH',__DIR__ . '/config/');

 (new config\Config())->load(CONFIG_PATH,'cache');

(new Cache())->set("ss","111");
//echo (new Cache())->get("ss");
