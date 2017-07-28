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

        $this->mq->send(2, ['hello' => 'world']);

        // Receive

        list ($message, $type) = $result = $this->mq->receive();

        $this->assertEquals(['hello' => 'world'], $message);

        $this->assertEquals($type, 2);

        $this->assertEquals($result['message'], ['hello' => 'world']);

        $this->assertEquals($result['type'], 2);

        $this->mq->destroy();

    }


    public function testReceiveRaw()
    {
        $this->mq->sendRaw(5, 'SysV');

        $result = $this->mq->receiveRaw();

        $this->assertEquals($result['message'], 'SysV');

        $this->assertEquals($result['type'], 5);

        $this->mq->destroy();

    }
    

    public function testReceiveJson()
    {
        $this->mq->sendJson(15, ['hello' => 'Wallace']);

        $result = $this->mq->receiveJson();

        $this->assertEquals(['hello' => 'Wallace'], $result['message']);

        $this->assertEquals(['hello' => 'Wallace'], $result[0]);

        $this->assertEquals(15, $result['type']);

        $this->assertEquals(15, $result[1]);
    }

}