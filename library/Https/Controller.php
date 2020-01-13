<?php
/**
 * 基类控制器
 */

namespace Library\Https;

class Controller
{
    protected $response;

    protected $code = 200;

    public function __construct()
    {
        $this->response = new Response();
    }

    public function json($data = [])
    {
        return $this->response->json($data);
    }

    public function index($params)
    {
        return $this->response->json(['hello' => 'saif']);
    }
}
