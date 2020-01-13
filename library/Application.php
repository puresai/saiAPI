<?php

namespace Library;

use Library\Exceptions\SaiException;
use Library\Https\Request;
use Library\Https\Response;

class Application
{
    private $config;

    private $request;

    public function __construct(Request $request, $config = [])
    {
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * 运行应用并输出数据
     * @return bool
     */
    public function run()
    {
        try {
            $response = $this->handleRequest($this->request);
            $response->send();
            return $response->exitStatus;
        } catch (SaiException $e) {
            $e->response($e->getCode(), [
                'line' => $e->getLine(),
                'msg' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ]);
            return false;
        }
    }

    /**
     * 处理请求
     * @param Request $request
     * @return mixed
     * @throws SaiException
     */
    public function handleRequest(Request $request)
    {
        $route = $request->resolve($this->config['route']??[]);

        $response = $request->runAction($route);
        /**
         * 执行结果赋值给$response->data，并返回给response对象
         */
        if ($response instanceof Response) {
            return $response;
        }

        throw new SaiException('Content format error');
    }
}
