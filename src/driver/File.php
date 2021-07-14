<?php
/**
 * Created by PhpStorm.
 * User: Print2
 * Date: 2021/7/14
 * Time: 15:30
 */

namespace cache\driver;


class File
{
    /**
     * 默认地址
     * @var string
     */
    protected $defaultPath  = "./data/cache";

    /**
     * 地址
     * @var
     */
    protected $dir;


    /**
    * 配置参数
    * @var array
    */
    protected $options = [
        'expire'        => 0,
        'prefix'        => '',
        'path'          => '',
        'hash_type'     => 'md5',
        'data_compress' => false,
        'tag_prefix'    => 'tag:',
        'serialize'     => [],
    ];

    /**
     * File constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {

        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }

        //设置默认地址
        if (empty($options['path'])) {
            $this->options['path'] = $this->getDefaultPath();
        } else {
            $this->createDir($options['path']);
        }

    }

    /**
     * 获取默认地址
     */
    protected function getDefaultPath()
    {
        //创建目录
        return $this->defaultPath = $this->createDir($this->defaultPath) == false ? "" : $this->defaultPath;

    }
    /**
     * 写入缓存
     * @access public
     * @param string            $name   缓存变量名
     * @param mixed             $value  存储数据
     * @param integer|\DateTime $expire 有效时间（秒）
     * @return bool
     */
    public function set($name, $value, $expire = null): bool
    {



        return true;
    }

    /**
     * 创建目录
     * @param string $dir
     * @return string
     */
    protected function createDir(string $dir)
    {

        if (!is_dir($dir)){
            try {
                mkdir($dir, 0755, true);

            } catch (\Exception $e) {
                // 创建失败

            }
        }
        return $dir;

    }



}