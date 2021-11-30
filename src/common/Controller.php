<?php

/**
 * @Description :
 *
 * @Date        : 2021/11/30 1:38 下午
 * @Author      : Jader
 */

namespace pm\common;

use pm\model\MongoMonitor;
use pm\model\MysqlMonitor;
use pm\model\SqliteMonitor;

class Controller
{
    protected $driver = 'mysql';
    protected $config = [];
    protected $requestDTO = [];
    public function __construct($driver, $config)
    {
        $this->driver = $driver;
        $this->config = $config;
    }

    public function getDb()
    {
        if ($this->driver === 'mysql') {
            return new MysqlMonitor();
        } elseif ($this->driver === 'mongodb') {
            return new MongoMonitor();
        } else {
            return new SqliteMonitor();
        }
    }

    protected function get($param, $default = '', $func = '')
    {
        $this->requestDTO[$param] = !isset($_GET[$param]) || empty($_GET[$param]) ? $default : $_GET[$param];
        if ($this->requestDTO[$param] !== '' && $func !== '') {
            $func($param);
        }
    }
    protected function post($param, $default = '', $func = '')
    {
        $this->requestDTO[$param] = !isset($_POST[$param]) || empty($_POST[$param]) ? $default : $_POST[$param];
        if ($default !== '' && $func !== '') {
            $func($param);
        }
    }

    protected function response($arr)
    {
        echo json_encode($arr);
        die();
    }
}
