<?php

use PHPLegends\SysV\MessageQueue;
use PHPUnit\Framework\TestCase;

class MessageQueueTest extends TestCase
{

    public function setUp()
    {
        $this->key = ftok(__DIR__, 'G');

        $this->mq = new MessageQueue($this->key);
    }    

    public function testSend()
    {
        $this->mq->send(1, ['hello' => 'world']);
    }

    public function testDestroy()
    {

        $this->assertTrue(MessageQueue::exists($this->key));

        $this->mq->destroy();

        $this->assertFalse(MessageQueue::exists($this->key));
    }


    public function testRemoveOnDestruct()
    {

        $this->assertTrue(MessageQueue::exists($this->key));

        $this->mq->removeOnDestruct();

        unset($this->mq);

        $this->assertFalse(MessageQueue::exists($this->key));

    }

    public function testReceive()
    {   

        $this->mq->send(5, ['hello' => 'world']);

        // Receive

        list ($message, $type) = $this->mq->receive(5, 4096, true, null);

        $this->assertEquals(['hello' => 'world'], $message);

        $this->mq->destroy();
    }

    

}