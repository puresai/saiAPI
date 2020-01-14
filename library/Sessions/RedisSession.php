<?php


namespace Library\Sessions;

use SessionHandler;

class RedisSession extends SessionHandler
{
    private $redis;
    
    private $lifeTime = 7200;
    
    private $config;

    private $prefix = 'SAIAPI_SESSION:';

    public function __construct($config)
    {
        $this->config = $config;
    }
    
    private function getRedisInstance()
    {
        if (empty($this->redis)) {
            $redis = new \Redis();
            $redis->connect($this->config['host'], $this->config['port'], $this->config['timeout']);
            if (!$this->config['auth']) {
                $redis->auth($this->config['auth']);
            }

            $this->redis = $redis;
        }
        return $this->redis;
    }

    public function read($id)
    {
        return $this->getRedisInstance()->get($this->prefix.$id);
    }

    public function write($id, $data)
    {
        if ($this->getRedisInstance()->setex($this->prefix.$id, $this->lifeTime, $data)) {
            return true;
        }

        return false;
    }

    public function destroy($id)
    {
        if($this->getRedisInstance()->delete($id)){//删除redis中的指定记录
            return true;
        }
        return false;
    }

    public function gc($maxlifetime)
    {
        return true;
    }

    public function __destruct()
    {
        session_write_close();
    }
}
