# delayqueue-php
[延迟队列](https://github.com/ouqiang/delay-queue)PHP客户端

[![Build Status](https://travis-ci.org/ouqiang/delayqueue-php.png)](https://travis-ci.org/ouqiang/delayqueue-php)

依赖
--------
* PHP5.4+
* ext-pcntl

安装
------------
```shell
composer require start-point/delayqueue-php
```

使用
------------

```php
<?php

use DelayQueue\Job;
use DelayQueue\Util\Time;
use DelayQueue\DelayQueue;

require 'vendor/autoload.php';

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
// 处理Job的类名
$className = 'Demo\\Handler\\OrderHandler';
try {
    // 添加一个Job到队列
    $delayQueue->push($className, $job);

    // 从队列中删除Job
    $delayQueue->delete('15702398321');
} catch (Exception $exception) {
    echo $exception->getMessage();
}
````

Job后台处理
----------
```shell
php vendor/bin/delayqueue-php -c /path/to/config.ini
```

创建Job处理类
----------

```php
<?php

namespace Demo\Handler;

use DelayQueue\Handler\AbstractHandler;

class OrderHandler extends AbstractHandler
{
    protected function perform()
    {
        // 未抛出异常视为成功, Job将会被删除
        // Job唯一标识
        // $this->id;
        
        // Job自定义内容
        // $this->body;
    }
}
```
