<?php
/**
 * Created by PhpStorm.
 * User: Print2
 * Date: 2021/7/12
 * Time: 15:37
 */

namespace cache;

use cache\driver\Redis;
use InvalidArgumentException;
abstract class Manager
{
    /** @var App */
    protected $app;

    /**
     * 驱动
     * @var array
     */
    protected $drivers = [];

    /**
     * 驱动的命名空间
     * @var string
     */
    protected $namespace = null;

    public function __construct()
    {

    }

    /**
     * 获取驱动实例
     * @param null|string $name
     * @return mixed
     */
    protected function driver(string $name = null)
    {
//        return new Redis();
        $name = $name ?: $this->getDefaultDriver();
        if (is_null($name)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL driver for [%s].',
                static::class
            ));
        }
//
        return $this->drivers[$name] = $this->getDriver($name);
    }

    /**
     * 获取驱动实例
     * @param string $name
     * @return mixed
     */
    protected function getDriver(string $name)
    {
        return $this->drivers[$name] ?? $this->createDriver($name);
    }

    /**
     * 获取驱动类型
     * @param string $name
     * @return mixed
     */
    protected function resolveType(string $name)
    {
        return $name;
    }

    /**
     * 获取驱动配置
     * @param string $name
     * @return mixed
     */
    protected function resolveConfig(string $name)
    {
        return $name;
    }

    /**
     * 获取驱动类
     * @param string $type
     * @return string
     */
    protected function resolveClass(string $type): string
    {

        if ($this->namespace || false !== strpos($type, '\\')) {

            $class = strpos($type, '\\') ? $type : $this->namespace .$type;
            if (class_exists($class)) {
                return $class;
            }
        }

        throw new InvalidArgumentException("Driver [$type] not supported.");
    }

    /**
     * 获取驱动参数
     * @param $name
     * @return array
     */
    protected function resolveParams($name): array
    {

        $config = $this->resolveConfig($name);

        return $config;
    }

    /**
     * 创建驱动
     *
     * @param string $name
     * @return mixed
     *
     */
    protected function createDriver(string $name)
    {
        $type = $this->resolveType($name);
        $params = $this->resolveParams($name);
        $class = $this->resolveClass($type);
        return new $class($params);
    }

    /**
     * 移除一个驱动实例
     *
     * @param array|string|null $name
     * @return $this
     */
    public function forgetDriver($name = null)
    {
        $name = $name ?? $this->getDefaultDriver();

        foreach ((array) $name as $cacheName) {
            if (isset($this->drivers[$cacheName])) {
                unset($this->drivers[$cacheName]);
            }
        }

        return $this;
    }

    /**
     * 默认驱动
     * @return string|null
     */
    abstract public function getDefaultDriver();


}