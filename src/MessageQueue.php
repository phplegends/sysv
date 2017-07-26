<?php

namespace PHPLegends\SysV;

use PHPLegends\SysV\Exceptions\Exception;

/**
 * Message Queue class
 * 
 * @author Wallace de Souza Vizerra <wallacemaxters@gmail.com>
*/
class MessageQueue
{
    /**
      * @var resource
    */
    protected $resource;
    
    /**
     * @var int
    */
    protected $key;
  
    /**
      * @var boolean
    */
    protected $removeOnDestruct = false;
  
    /**
     * 
     * @param int $key
     * @param int $perms
    */
    public function __construct($key, $perms = 0666)
    {
        $this->resource = msg_get_queue($key, $perms);

        $this->key = $key;
    }

    /**
      * @throws \PHPLegends\SysV\Exceptions\Exception\Exception
      * @return resource
    */
    protected function getResource()
    {

        if ($this->resource === null)
        {
            throw new Exception('The current queue has been destroyed');
        }

        return $this->resource;
    }
  
    /**
     * Send data to message queue
     *
     * @param int $msgtype
     * @param mixed $message
     * @param boolean $serialize
     * @param boolean $blocking
    */
    public function send($msgtype, $message, $serialize = true, $blocking = true)
    {
        $sent = @msg_send($this->getResource(), $msgtype, $message, $serialize, $blocking, $error);

        if ($sent === false) {

            throw new Exception("Error occurred #{$error}");
        }

        return $this;
    }

  
    /**
     * Send raw data, without serialize
     *
     * @param int $msgtype
     * @param string $message
     * @param boolean $blocking
    */
    public function sendRaw($msgtype, $message, $blocking = true)
    {
        return $this->send($msgtype, $message, false, $blocking);
    }
    
    /**
     * Receive data
     *
     * @throws \PHPLegends\SysV\Exceptions\Exception
     * @param int $desiredType
     * @param int $maxsize
     * @param boolean $unserialize
     * @param int $flags
     * @return array
    */
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

    /**
     * Get stat
     * @return array
    */
    public function stat()
    {
        return msg_stat_queue($this->getResource());
    }
  
    /**
      * @param array $data
      * @return self
    */
    public function set(array $data)
    {
        msg_set_queue($this->getResource(), $data);

        return $this;
    }

    public static function exists($key)
    {
        return msg_queue_exists($key);
    }

    
    /**
    * Destroy the MessageQueue
    * 
    * @return self
    */
    public function destroy()
    {
        msg_remove_queue($this->getResource());

        $this->resource = null;

        return $this;
    }
    
    /**
      * Mark the message queue to remove on __destruct
      * 
      * @param boolean $removeOnDestruct
      * @return self
    */
    public function removeOnDestruct($removeOnDestruct = true)
    {
        $this->removeOnDestruct = $removeOnDestruct;

        return $this;
    }
    
    /**
      * Destroy message queue if is marked
      * @return void
    */
    public function __destruct()
    {
        $this->removeOnDestruct && $this->destroy();
    }
}