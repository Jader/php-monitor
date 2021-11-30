<?php

/**
 * @Description :
 *
 * @Date        : 2021/11/30 2:00 下午
 * @Author      : Jader
 */

namespace pm\model\common;

interface iModelInterface
{
    public function insertData($data);

    public function getList($dto);

    public function findOne($dto);

    public function findFlame($dto);

    public function findByUrl($dto);
}
