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

        if (substr( $this->options['path'],-1) != DIRECTORY_SEPARATOR) $this->options['path'] .= DIRECTORY_SEPARATOR;

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
        //获取有效期时间
        if (empty($expire)) {
            $expire = $this->options['expire'];
        }
        $hash_type = strtolower($this->options['hash_type']);
        //缓存名
        switch ($hash_type) {
            case 'md5':
                //缓存名
                $name = md5($name);
                break;
            default:
                $name = md5($name);
        }
        $data = serialize($value);
        if ($this->options["data_compress"] && function_exists("gzcompress")) {
            gzcompress($data,3);
        }
        $data   = "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n" . $data;
        $path = $this->options['path'].'/'.$name.'.php';
        $result = file_put_contents($path,$data);
        if ($result) {
            clearstatcache();
            return true;
        }
        return false;
    }

    /**
     * @param $name 名称
     * @param null $default
     * @return array|bool|null
     */

    public function get($name, $default = null)
    {
        //获取数据
        $raw = $this->getRaw($name);
        $data = "";
        if(is_array($raw) && !empty($raw)){
            $data = $this->unserialize($raw);
            if ($data == false) return "";
        }

        return $data;

    }

    public function has($name)
    {
        if ($this->getRaw($name) == false ||$this->getRaw($name) == null ) {
            return false;
        }
        return true;
    }

    /**
     * 返回序列化
     * @param $raw
     * @return bool
     */
    public function unserialize($raw)
    {
        if (!is_array($raw)){
            return false;
        }

        if (empty($this->options["serialize"])){
            $unserialize = "unserialize";
        } else {
            $unserialize = $this->options["serialize"][1];
        }
        $content = $raw['content'];
        return $unserialize($content);

    }

    /**
     * 获取缓存数据
     * @param $name
     * @return array|bool|null
     */
    public function getRaw($name)
    {

        $hash_type = strtolower($this->options['hash_type']);
        //缓存名
        switch ($hash_type) {
            case 'md5':
                //缓存名
                $name = md5($name);
                break;
            default:
                $name = md5($name);
        }
        $path = $this->options['path'].'/'.$name.'.php';
        if (!file_exists($path)){
            return false;
        }
        $result = @file_get_contents($path,true );
        if ($result == true) {
            // 获取有效期时间戳’
            $expire = (int)substr($result,8, 12);
            //删除过期的文件
            if (0 != $expire && time() - $expire > filemtime($path)) {

                //删除缓存文件
                $this->unlink($path);
                return true;
            }

            $content = substr($result,32);

            if ($this->options["data_compress"] && function_exists("gzuncompresss")) {
                //解压
                gzuncompress($content);
            }

            return is_string($content) ? ['content' => $content, 'expire' => $expire] : null;


        }

        return false;


    }

    /**
     * 删除文件
     * @param $path
     * @return bool
     */
    public function unlink($path)
    {
        try {

          if (file_exists($path))unlink($path);

        } catch (\Exception $e) {
            // 创建失败
            return false;

        }
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