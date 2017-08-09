<?php

use DelayQueue\Job;
use DelayQueue\Util\Time;
use DelayQueue\DelayQueue;

require '../vendor/autoload.php';

$job = new Job();
$job->setTopic('order');
$job->setId('15702398321');
$job->setDelay(1 * Time::HOUR);
$job->setTtr(60 * Time::SECOND);
$job->setBody([
   'uid' => 10829378,
    'created' => 1498657365,
]);