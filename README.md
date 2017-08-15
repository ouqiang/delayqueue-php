# delayqueue-php
[延迟队列](https://github.com/ouqiang/delay-queue)PHP客户端

[![Build Status](https://travis-ci.org/ouqiang/delayqueue-php.png)](https://travis-ci.org/ouqiang/delayqueue-php)
[![Latest Stable Version](https://poser.pugx.org/start-point/delayqueue-php/version)](https://packagist.org/packages/start-point/delayqueue-php)
[![Total Downloads](https://poser.pugx.org/start-point/delayqueue-php/downloads)](https://packagist.org/packages/start-point/delayqueue-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ouqiang/delayqueue-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ouqiang/delayqueue-php/?branch=master)
[![Latest Unstable Version](https://poser.pugx.org/start-point/delayqueue-php/v/unstable)](//packagist.org/packages/start-point/delayqueue-php)
[![License](https://poser.pugx.org/start-point/delayqueue-php/license)](https://packagist.org/packages/start-point/delayqueue-php)
[![composer.lock available](https://poser.pugx.org/start-point/delayqueue-php/composerlock)](https://packagist.org/packages/start-point/delayqueue-php)

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

# 运行Worker处理Job


### 单个Worker

```shell
php vendor/bin/delayqueue-php -c /path/to/config.ini
```

### PHP实现多个Worker

```php
#!/usr/bin/env php
<?php

$workerNum = 10;
$command = '/path/to/vendor/bin/delayqueue-php';
$args = ['-c', '/path/to/config.ini'];
for ($i = 0; $i < $workerNum; $i++) {
    $pid = pcntl_fork();
    if ($pid < 0) {
        // fork失败
    } else if ($pid === 0) {
        // 子进程
        pcntl_exec($command, $args);
        break;
    }
}
```

### Supervisor配置多个Worker


```ini
[program:delayqueue-php]
command=/path/to/vendor/bin/delayqueue-php -c /path/to/config.ini
process_name=%(program_name)s_%(process_num)s
numprocs = 10
numprocs_start = 1
autostart=true                           
autorestart=true                        
startretries=3                       
redirect_stderr = true
stdout_logfile=/var/log/supervisor/delayqueue-php/out.log
```

创建Job处理类
----------

```php
<?php

namespace Demo\Handler;

use DelayQueue\Handler\AbstractHandler;

// 必须继承DelayQueue\Handler\AbstractHandler, 并实现方法perform
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
