<?php

namespace PHPLegends\SysV;

use PHPLegends\SysV\Exceptions\Exception;

class MessageQueue
{
    
    protected $resource;

    protected $key;

    protected $destroyOnDestruct = false;

    public function __construct($key, $perms = 0666)
    {
        $this->resource = msg_get_queue($key, $perms);
    }


    protected function getResource()
    {

        if ($this->resource === null)
        {
            throw new Exception('The current queue has been destroyed');
        }

        return $this->resource;
    }


    public function send($msgtype, $message, $serialize = true, $blocking = true)
    {
        $sent = @msg_send($this->getResource(), $msgtype, $message, $serialize, $blocking, $error);

        if ($sent === false) {

            throw new Exception("Error occurred #{$error}");
        }

        return $this;
    }


    public function sendRaw($msgtype, $message, $blocking = true)
    {
        return $this->send($msgtype, $message, false, $blocking);
    }

    public function receive($desiredType, $maxsize, $unserialize = true, $flags = 0)
    {

        $message = null;

        $msgtype = null;

        $received = msg_receive($this->getResource(), $desiredType, $type, $maxsize, $message, $unserialize, $flags, $error);


        if ($received === false) {

            throw new Exception("Error occurred #{$error}");
        }

        return compact('message', 'type') + [$message, $type];
    }


    public function receiveRaw($desiredmsgtype, $maxsize, $flags = 0)
    {
        return $this->receive($desiredmsgtype, $maxSize, false, $flags);
    }


    public function state()
    {
        return msg_stat_queue($this->getResource());
    }

    public function set(array $data)
    {
        msg_set_queue($this->getResource(), $data);

        return $this;
    }

    public static function exists($key)
    {
        return msg_queue_exists($key);
    }


    public function destroy()
    {
        msg_remove_queue($this->getResource());

        $this->resource = null;

        return $this;
    }

    public function destroyOnDestruct()
    {
        $this->destroyOnDestruct = true;
    }

    public function __destruct()
    {
        $this->destroyOnDestruct && $this->destroy();
    }
}