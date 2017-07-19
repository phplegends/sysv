<?php

use PHPLegends\SysV\Semaphore;
use PHPUnit\Framework\TestCase;
use PHPLegends\SysV\Exceptions\Exception;

class SemaphoreTest extends TestCase
{

    public function setUp()
    {
        $this->key = ftok(__FILE__, 'g');

        $this->sem = new Semaphore($this->key, 2, 0666, 0);
    }


    public function testGetMaxAcquire()
    {

        $this->assertEquals(2, $this->sem->getMaxAcquire());
    }

    public function testAcquire()
    {
        $this->assertTrue($this->sem->acquire(true));

        $this->assertEquals(1, $this->sem->getCountAcquire());

        $this->assertTrue($this->sem->acquire(true));

        $this->assertEquals(2, $this->sem->getCountAcquire());

        $this->assertFalse($this->sem->acquire(true));
        
        $this->assertTrue($this->sem->release());

        $this->assertEquals(1, $this->sem->getCountAcquire());

        $this->assertTrue($this->sem->release());

        $this->assertEquals(0, $this->sem->getCountAcquire());

        $this->assertFalse($this->sem->release());

        $this->assertEquals(0, $this->sem->getCountAcquire());

    }

    public function testRemove()
    {

        $this->sem->remove();

        try{

            $this->sem->acquire();

        } catch (Exception $e) {

            $this->assertInstanceOf(
                'PHPLegends\SysV\Exceptions\Exception',
                $e
            );      
        }
    }


    public function testGetKey()
    {
        $this->assertEquals($this->key, $this->sem->getKey());
    }

}