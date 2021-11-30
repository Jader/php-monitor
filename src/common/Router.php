<?php

/**
 * @Description :
 *
 * @Date        : 2021/11/30 1:44 下午
 * @Author      : Jader
 */

namespace pm\common;

use Illuminate\Database\Capsule\Manager as Capsule;

class Router
{
    public $_driver = '';
    public $_config = [];
    public function __construct(array $config)
    {
        if ($config['debug']) {
            ini_set('display_errors', 1);
        }
        $this->_config = $config;
        $this->initDb($config['save']);
    }

    public function initDb($db)
    {
        //init ORM
        $dbName = strtolower($db['driver']);
        $this->_driver = $dbName;
        $capsule = new Capsule();
        if ($dbName === 'mongodb') {
            $capsule->getDatabaseManager()->extend('mongodb', function ($config, $name) {
                $config['name'] = $name;
                return new \Jenssegers\Mongodb\Connection($config);
            });
        }
        $capsule->addConnection($db);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function run()
    {
        $r = isset($_GET['r']) && !empty($_GET['r']) ? explode('/', $_GET['r']) : [];
        if (!isset($r[1])) {
            throw new \InvalidArgumentException('URL error, like index.php?r=api/test.', 1);
        } else {
            if (preg_match('/^[a-zA-Z]{1,32}$/U', $r[0]) === 1) {
                $c = ucfirst(strtolower($r[0]));
                $controller = '\pm\controller\\' . $c . 'Controller';
            } else {
                throw new \InvalidArgumentException('Controller names can only contain letters.', 1);
            }
            if (preg_match('/^[a-zA-Z-]{0,32}$/U', $r[1]) === 1) {
                $actionName = preg_replace_callback('/([-]+([a-z]{1}))/i', function ($matches) {
                    return strtoupper($matches[2]);
                }, strtolower($r[1]));
                $action = $actionName . 'Action';
            } else {
                throw new \InvalidArgumentException("Action names can only contain letters and '-'.", 1);
            }
            if (class_exists($controller)) {
                $myclass = new $controller($this->_driver, $this->_config);
                if (method_exists($myclass, $action)) {
                    $myclass->$action();
                } else {
                    throw new \InvalidArgumentException('Action not found.', 1);
                }
            } else {
                throw new \InvalidArgumentException('Controller not found.', 1);
            }
        }
    }
}
