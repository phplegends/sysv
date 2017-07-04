<?php

namespace PHPLegends\Semaphore\SharedMemory;

use PHPLegends\Semaphore\Exceptions\Exception as BaseException;

class SharedMemory
{
	/**
	 * @var resource
	*/
	protected $resource = null;

	public function __construct($key, $memsize = 10000, $perm = 0666)
	{
		$this->resource = shm_attach($key, $memsize, $perm);
	}

	public function get($variable_key)
	{
		return shm_get_var($this->getResource(), $variable_key);
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

			throw new PutVariableException('Failure while put variable ' . $variable_key);
		}

		return $this;
	}

	/**
	* @param int $variable_key
	* @return self
	*/
	public function remove($variable_key)
	{
		$result = shm_remove_var($this->getResource(), $variable_key);

		$this->resource = null;

		return $result;
	}

	public function getResource()
	{
		if ($this->resource === null) {

			throw new \RunTimeException('Shared Memory has been removed!');
		}

		return $this->resource;
	}

	
}