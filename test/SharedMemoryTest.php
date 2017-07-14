<?php

use PHPLegends\SysV\Exceptions\SharedMemoryException;
use PHPLegends\SysV\SharedMemory\SharedMemory;
use PHPUnit\Framework\TestCase;

class SharedMemoryTest extends TestCase
{

	public function setUp()
	{
		$key = ftok(__DIR__, 'G');

		$this->shm = new SharedMemory($key, 10000, 0777);
	}    

	public function testPut()
	{  
        $this->assertFalse($this->shm->has(1));

        // put 

		$this->shm->put(1, 'wallace');

        $this->assertTrue($this->shm->has(1));
	}

    public function testGet()
    {
        $value = $this->shm->get(1);

        $this->assertEquals('wallace', $value);
    }


    public function testRemove()
    {

        $this->assertTrue($this->shm->has(1));

        $this->assertTrue(
            $this->shm->remove(1)
        );

        $this->assertFalse($this->shm->has(1));

        $this->assertFalse($this->shm->remove(1));
    }


    public function testDestroy()
    {
        $this->shm->destroy();

        try{

            $this->shm->get(1);

        } catch (\Exception $e) {

            $this->assertTrue($e instanceof SharedMemoryException);

        }

    }

}