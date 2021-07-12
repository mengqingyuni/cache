<?php
namespace cache;
require __DIR__ . '/../vendor/autoload.php';

(new Cache())->set("ss","111");

echo (new Cache())->get("ss");