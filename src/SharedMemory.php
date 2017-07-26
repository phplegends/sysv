<?php

namespace PHPLegends\SysV;

use PHPLegends\SysV\Exceptions\SharedMemoryException;

/**
 * Shared Memory
 *
 * @author Wallace de Souza Vizerra <wallacemaxters@gmail.com>
*/
class SharedMemory
{
    /**
     * @var resource
    */
    protected $resource = null;

    /**
     * @param int $key
     * @param int $memsize
     * @param int $perm
    */
    public function __construct($key, $memsize = 10000, $perm = 0666)
    {
        $memsize = (int) $memsize;

        if ($memsize <= 0) {

            throw new SharedMemoryException('Segment size must be greater than zero');
        }

        $this->resource = shm_attach($key, $memsize, $perm);
    }

    /**
      * @todo On warning, returns false. The correct is "NULL"
      * @param int $variable_key
      * @return mixed
    */
    public function get($variable_key)
    {
        return @shm_get_var($this->getResource(), $variable_key);
    }

    /**
     * Checks whether a specific key exists inside a shared memory segment.
     *
     * @param int $variable_key
     * @return boolean
    */
    public function has($variable_key)
    {
        return shm_has_var($this->getResource(), $variable_key);
    }

    /**
     * Put variable in shared memory
     *
     * @param int $variable_key
     * @param mixed $value 
    */
    public function put($variable_key, $value)
    {
        $boolean = @shm_put_var($this->getResource(), $variable_key, $value);

        if ($boolean === false) {

            throw new SharedMemoryException('Failure while put variable ' . $variable_key);
        }

        return $this;
    }

    /**
      * Remove variable in shared memory
      *
      * @param int $variable_key
      * @return self
    */
    public function remove($variable_key)
    {
        return @shm_remove_var($this->getResource(), $variable_key);
    }

    /**
      * Destroy shared memory
      *
      * @return self
    */
    public function destroy()
    {
        @shm_remove($this->getResource());

        $this->resource = null;

        return $this;
    }

    /**
     * Gets the resource of current shared memory
     *
     * @throws \PHPLegends\Semaphore\Exceptions\SharedMemoryException
     * @return resource
    */
    protected function getResource()
    {
        if ($this->resource === null) {

            throw new SharedMemoryException('Shared Memory has been removed!');
        }

        return $this->resource;
    }
  
    /**
      * @return void
    */
    public function __destruct()
    {
        is_resource($this->resource) && shm_detach($this->getResource());
    }
}