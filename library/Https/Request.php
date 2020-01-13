<?php
/**
 * 处理请求
 */

namespace Library\Https;

use Library\Components\Base;
use Library\Exceptions\NotFoundException;

class Request extends Base
{

    const HTTP_METHOD_GET = 'GET';

    /**
     * 获取请求方法
     * @return string
     */
    public function getMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return self::HTTP_METHOD_GET;
    }

    /**
     * 请求头
     * @param $name
     * @param null $defaultValue
     * @return mixed|null
     */
    public function getHeader($name, $defaultValue = null)
    {
        $name = ucfirst($name);
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            p($_SERVER);
            return $headers[$name]?? $defaultValue;
        }
        $name = strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$name]?? ($_SERVER['HTTP_'.$name] ?? $defaultValue);
    }

    /**
     * 获取get参数
     * @param null $name
     * @param null $defaultValue
     * @return |null
     */
    public function get($name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $this->getQueryParams();
        }
        return $this->getQueryParam($name, $defaultValue);
    }

    public function getQueryParam($name, $defaultValue = null)
    {
        $params = $this->getQueryParams();
        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }

    public function getQueryParams()
    {
        if (empty($this->queryParams)) {
            return $this->queryParams = $_GET;
        }
        return $this->queryParams;
    }

    /**
     * 获取post参数
     * @param null $name
     * @param null $defaultValue
     * @return array|mixed|null
     */
    public function post($name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $this->getBodyParams();
        }
        return $this->getBodyParam($name, $defaultValue);
    }

    public function getBodyParam($name, $defaultValue = null)
    {
        $params = $this->getBodyParams();
        if (is_object($params)) {
            try {
                return $params->{$name};
            } catch (\Exception $e) {
                return $defaultValue;
            }
        }
        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }

    public function getBodyParams()
    {
        $contentType = strtolower($this->getHeader('Content-Type'));
        // p($contentType);
        if (strpos($contentType, 'multipart/form-data') === false) {
            $this->bodyParams = \json_decode(file_get_contents("php://input"), true);
        } else {
            $this->bodyParams = $_POST;
        }
        
        return $this->bodyParams?? [];
    }

    /**
     * get参数数组
     */
    private $queryParams = [];

    /**
     * post参数数组
     */
    private $bodyParams = [];

    private $routeParams = [];

    private $method;

    private $route = [];

    
    /**
     * 控制器处理
     * @param $route
     * @return mixed
     * @throws NotFoundException
     */
    public function runAction($route)
    {
        if (array_key_exists($route, $this->route)) {
            $route = $this->route[$route];
        }

        $match = explode('/', $route);
        $match = array_filter($match);

        // 处理$route=/
        if (empty($match)) {
            $match = ['index'];
            $controller = $this->createController($match);
            $action = 'index';

        // 处理$route=index
        } elseif (count($match) < 2) {
            $controller = $this->createController($match);
            $action = 'index';
        } else {
            $action = array_pop($match);
            $controller = $this->createController($match);

            if (!method_exists($controller, $action)) {
                throw new NotFoundException("method not found:".$action);
            }
        }

        return $controller->$action(array_merge($this->getQueryParams(), $this->getBodyParams()));
    }

    // app应用控制器命名空间
    private $_controllerNameSpace = 'App\\Https\\Controllers';

    // 之前定义的基类控制器
    private $_baseController = 'Library\\Https\\Controller';

    public function createController($match)
    {
        $controllerName = $this->_controllerNameSpace;

        foreach ($match as $namespace) {
            $controllerName .= '\\'.ucfirst($namespace);
        }

        $controllerName = $controllerName.'Controller';

        if (!class_exists($controllerName)) {
            if ($controllerName == $this->_controllerNameSpace.'IndexController') {
                return new $this->_baseController;
            }
            throw new NotFoundException("controller not found:".$controllerName);
        }

        return new $controllerName;
    }

    /**
     * 返回不含参数的REQUEST_URI地址与GET参数
     */
    public function resolve($route)
    {
        $this->route = $route;
        return $this->getPathUrl();
    }


    private $pathUrl;

    /**
     * 获取请求地址
     * @return bool|mixed|string
     */
    public function getPathUrl()
    {
        if (is_null($this->pathUrl)) {
            $arr = parse_url(trim($_SERVER['REQUEST_URI'], '/'));
            $this->pathUrl = $arr['path']??'';
        }

        return $this->pathUrl;
    }
}
