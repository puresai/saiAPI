<?php


namespace Library\Sessions;

use SessionHandler;

class FileSession extends SessionHandler
{
    private $savePath;

    public function __construct($config)
    {
        $this->savePath = $config['savePath'];
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }
        // p($this->savePath);
    }

    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        return file_exists("$this->savePath/sess_$id")?
        (file_get_contents("$this->savePath/sess_$id")) : serialize(null);
    }

    public function write($id, $data)
    {
        return (file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true);
    }

    public function destroy($id)
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc($maxlifetime)
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

    public function __destruct()
    {
        session_write_close();
    }
}