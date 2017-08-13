<?php


namespace DelayQueue\Tests;

use DelayQueue\Container\Container;
use DelayQueue\Job;
use DelayQueue\DelayQueue;
use DelayQueue\Util\Time;
use DelayQueue\Handler\AbstractHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class DelayQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DelayQueue
     */
    protected $delayQueue;
    protected $jobId = '15702398321';
    protected $topic = 'order';

    protected function setUp()
    {
        $this->delayQueue = new DelayQueue('http://127.0.0.1:9277');
    }

    public function providerJob()
    {
        $job = new Job();
        $job->setTopic($this->topic);
        $job->setId($this->jobId);
        $job->setDelay(5 * Time::SECOND);
        $job->setTtr(60 * Time::SECOND);
        $job->setBody([
            'uid' => 10829378,
            'created' => 1498657365,
        ]);

        return [
            [
                $job
            ]
        ];
    }

    /**
     * @dataProvider providerJob
     *
     * @param Job $job
     * @throws \DelayQueue\Exception\ClassNotFoundException
     * @throws \DelayQueue\Exception\SubClassException
     */
    public function testPushException(Job $job)
    {
        $this->setExpectedException('DelayQueue\Exception\ClassNotFoundException');
        $this->delayQueue->push('testHandler', $job);

        $this->setExpectedException('DelayQueue\Exception\SubClassException');
        $this->delayQueue->push('DelayQueue\Tests\DelayQueueTest', $job);
    }

    /**
     * @dataProvider providerJob
     * @param Job $job
     * @throws \DelayQueue\Exception\ClassNotFoundException
     * @throws \DelayQueue\Exception\SubClassException
     */
    public function testPush(Job $job)
    {
        $className = '\\DelayQueue\\Tests\Handler';
        $this->delayQueue->push($className, $job);
        sleep(3 * Time::SECOND);
        $this->delayQueue->delete($job->id);
    }

    /**
     *
     * @dataProvider providerJob
     * @param Job $Job
     * @throws \DelayQueue\Exception\ClassNotFoundException
     * @throws \DelayQueue\Exception\SubClassException
     */
    public function testPop(Job $job)
    {
        $job->id = '56352695584';
        $oldTimeout = $this->delayQueue->getTimeout();
        $this->delayQueue->setTimeout(20);
        $className = '\\DelayQueue\\Tests\Handler';
        $cloneJob = clone $job;
        $this->delayQueue->push($className, $cloneJob);
        $data = $this->delayQueue->pop([$this->topic]);
        $this->assertEquals($job->id, $data['id']);
        $this->assertEquals($className, $data['className']);
        $this->assertEquals($job->body, $data['body']);
        /** @var AbstractHandler $class */
        $container = new Container();
        $container->set('logger', function () {
            $logger = new Logger('delay-queue');
            $logger->pushHandler(
                new StreamHandler(
                    'php://stdout',
                    Logger::INFO,
                    true,
                    null,
                    true)
            );

            return $logger;
        });
        $class = new $data['className']($container);
        $class->setId($data['id']);
        $class->setBody($data['body']);
        $class->run();
        $this->delayQueue->finish($job->id);
        $this->delayQueue->setTimeout($oldTimeout);
    }
}