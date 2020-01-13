<?php
/**
 * 数据输出
 */

namespace Library\Https;

use Library\Components\Base;

class Response extends Base
{
    public $code = 0;

    public $result = [];

    public $msg = "success";

    public function send()
    {
        header('Content-Type:application/json; charset=utf-8');
        echo \json_encode([
            'data' => $this->result,
            'msg' => $this->msg,
            'code' => $this->code,
            'timestamp' => time()
        ]);
    }

    public function json($data = [])
    {
        $this->result = array_merge($this->result, $data);
        return $this;
    }
}
