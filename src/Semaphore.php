<?php

namespace PHPLegends\SysV;

use PHPLegends\SysV\Exceptions\Exception;


/**
 * Represents the "sem_" functions
 * 
 * @author Wallace de Souza Vizerra <wallacemaxters@gmail.com>
 * @see http://php.net/manual/pt_BR/function.sem-get.php
 * 
 * */

class Semaphore
{

    /**
     * @var int 
     * */
    protected $countAcquire = 0;

    /**
     * 
     * @var int
     * */
    protected $key;

    /**
     * 
     * @var int
     * */

    protected $maxAcquire;

    /**
     * 
     * @var resource
    */
    protected $resource;

    /**
     * 
     * @var boolean
     * */

    protected $removeOnDestruct = false;


    /**
     * 
     * @param int $key
     * @param int $maxAcquire
     * @param int $perm
     * @param int $autoRelease
     * 
     * */
    public function __construct($key, $maxAcquire = 1, $perm = 0666, $autoRelease = 1)
    {
        $this->maxAcquire = (int) $maxAcquire;

        $this->key = (int) $key;

        $this->resource = sem_get($key, $this->maxAcquire, $perm, $autoRelease);
    }


    /**
     * Acquires
     * 
     * @param boolean $noWait
     * @return boolean
     * */

    public function acquire($noWait = false)
    {

        if ($this->supportsNoWait())
        {
            $result = sem_acquire($this->getResource(), $noWait);

        } elseif ($noWait === true && $this->getCountAcquire() === $this->getMaxAcquire()) {

            // if 'no wait' is not supported, checking via countAcquire

            $result = false;

        } else {

            $result = sem_acquire($this->getResource());    
        }

        $result && $this->countAcquire++;

        return $result;
    }

    /**
     * Release
     * 
     * @return boolean
     * */
    public function release()
    {
        $result = @sem_release($this->getResource());

        $result && $this->countAcquire--;

        return $result;
    } 

    /**
     * Removes the current semaphore
     * 
     * @return self
     * */
    public function remove()
    {
        sem_remove($this->getResource());

        $this->resource = null;

        return $this;
    }

    /**
     * Marks this instance for remove semaphore on destruct (or not)
     * 
     * @param boolean $removeOnDestruct
     * 
     * */
    public function removeOnDestruct($removeOnDestruct = true)
    {
        $this->removeOnDestruct = $removeOnDestruct;

        return $this;
    }
    
    /**
     * @return int
     * */
    public function getKey()
    {
        return $this->key;
    }


    /**
     * 
     * @return resource
     * */
    protected function getResource()
    {
        if ($this->resource === null)
        {
            throw new Exception("Semaphore not found");
        }

        return $this->resource;
    }


    /**
     * Current number of acquire
     * 
     * @return int
     * */
    public function getCountAcquire()
    {
        return $this->countAcquire;
    }

    /**
     * Get max number of acquire
     * 
     * @return int
     * */
    public function getMaxAcquire()
    {
        return $this->maxAcquire;
    }

    /**
     * Check if current php version supports "no wait" call
     * 
     * @see http://php.net/manual/pt_BR/function.sem-acquire.php
     * @return boolean
     * */
    public function supportsNoWait()
    {
        return PHP_VERSION_ID >= 50610;
    }

    /**
     * remove semaphore if is marked
     * 
     * */
    public function __destruct()
    {
        $this->removeOnDestruct && $this->remove();
    }

}