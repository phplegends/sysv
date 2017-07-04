<?php

use PHPUnit\Framework\TestCase;

use PHPLegends\Semaphore\SharedMemory\SharedMemory;

class SharedMemoryTest extends TestCase
{
	public function setUp()
	{
		$key = ftok(__DIR__, 'G');

		$this->shm = new SharedMemory($key);
	}    

	public function testSet()
	{
		$this->shm->put();
	}
}