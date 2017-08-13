<?php

use DelayQueue\Job;
use DelayQueue\Util\Time;
use DelayQueue\DelayQueue;

require __DIR__ . '/../vendor/autoload.php';

$job = new Job();
$job->setTopic('order');
$job->setId('15702398321');
$job->setDelay(1 * Time::MINUTE);
$job->setTtr(20 * Time::SECOND);
$job->setBody([
   'uid' => 10829378,
  'created' => 1498657365,
]);


$delayQueue = new DelayQueue('http://127.0.0.1:9277');
$className = 'Demo\\Handler\\OrderHandler';
try {
    // 添加一个Job到延迟队列
    $delayQueue->push($className, $job);
} catch (Exception $exception) {
    echo $exception->getMessage();
}

