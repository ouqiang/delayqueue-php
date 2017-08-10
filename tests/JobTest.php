<?php

namespace DelayQueue\Tests;


use DelayQueue\Job;
use DelayQueue\Util\Time;
use PHPUnit_Framework_TestCase;

class JobTest extends PHPUnit_Framework_TestCase
{
    public function providerJob()
    {
        $job = new Job();
        $job->setTopic('order');
        $job->setId('156236252625');
        $job->setDelay(1 * Time::MINUTE);
        $job->setTtr(120 * Time::SECOND);
        $body =  [
            'uid' => 12562,
        ];
        $job->setBody($body);

        return [
            [
                $job,
            ],
        ];
    }

    public function testAppendValueToBody()
    {
        $job = new Job();
        $key   = 'className';
        $value = 'Test';
        $job->appendValueToBody($key, $value);
        $this->assertArrayHasKey($key, $job->body);
        $this->assertEquals($value, $job->body[$key]);
        $job->setBody([]);
    }

    /**
     * @dataProvider providerJob
     */
    public function testToArray(Job $job)
    {
        $arr = $job->toArray();
        $this->assertArrayHasKey('id', $arr);
        $this->assertEquals('156236252625', $arr['id']);
        $this->assertArrayHasKey('topic', $arr);
        $this->assertEquals('order', $arr['topic']);
        $this->assertArrayHasKey('delay', $arr);
        $this->assertEquals(1 * Time::MINUTE, $arr['delay']);
        $this->assertArrayHasKey('ttr', $arr);
        $this->assertEquals(120 * Time::SECOND, $arr['ttr']);
        $this->assertArrayHasKey('body', $arr);
        $this->assertJsonStringEqualsJsonString(json_encode(['uid' => 12562]), $arr['body']);

        return $job;
    }

    /**
     * @dataProvider providerJob
     */
    public function testJsonSerialize(Job $job)
    {
        $this->assertJsonStringEqualsJsonString(json_encode($job->toArray()), json_encode($job));
    }
}