<?php

/**
 * @Description :
 *
 * @Date        : 2021/11/30 1:46 下午
 * @Author      : Jader
 */

namespace pm\controller;

use pm\common\Controller;

class LoginController extends Controller
{
    public function accountAction()
    {
        $post = json_decode(file_get_contents('php://input', 'r'), true);

        $userName = isset($post['userName']) ? $post['userName'] : '';
        $password = isset($post['password']) ? $post['password'] : '';
        $rtn = [
            'status' => 'error',
            'type' => 'account',
            'currentAuthority' => 'guest'
        ];
        if (!empty($this->config['user']) && isset($this->config['user']['mode']) && !empty($userName) && !empty($password)) {

            switch ($this->config['user']['mode']) {
                case 'api':
                    $postData = http_build_query(['u' => $userName, 'p' => $password]);
                    $options = [
                        'http' => [
                            'method' => 'POST',
                            'header' => 'Content-type:application/x-www-form-urlencoded',
                            'content' => $postData,
                            'timeout' => 15 * 60
                        ]
                    ];
                    $context = stream_context_create($options);
                    $result = file_get_contents($this->config['user']['api'], false, $context);
                    if ($result == 'true') {
                        $rtn = [
                            'status' => 'ok',
                            'type' => 'account',
                            'currentAuthority' => 'admin'
                        ];
                        session_start();
                        $_SESSION['ACCOUNT'] = [
                            'name' => $userName,
                            'time' => time()
                        ];
                    }
                    break;
                case 'default':
                default:
                    foreach ($this->config['user'] as $val) {
                        if ($val['account'] === $userName && $val['password'] === $password) {
                            $rtn = [
                                'status' => 'ok',
                                'type' => 'account',
                                'currentAuthority' => 'admin'
                            ];
                            session_start();
                            $_SESSION['ACCOUNT'] = [
                                'name' => $userName,
                                'time' => time()
                            ];
                            break;
                        }
                    }
                    break;
            }

        }
        return $this->response($rtn);
    }

    public function outAction()
    {
        session_start();
        unset($_SESSION['ACCOUNT']);
    }
}
