<?php

declare(ticks = 1);

namespace DelayQueue\Process;

use DelayQueue\Container\ContainerAwareTrait;
use Exception;
use DelayQueue\Handler\AbstractHandler;

class Worker
{
    use ContainerAwareTrait;

    /**
     * @var array 轮询队列
     */
    protected $topics;

    /**
     * @var bool 是否在下次循环中退出
     */
    protected $shutdown = false;

    public function setTopics(array $topics)
    {
        $this->topics = $topics;
    }


    public function run()
    {
        $this->registerSignalHandlers();
        while(true) {
            if ($this->shutdown) {
                break;
            }
            $data = null;
            try {
                $data = $this->delayQueue->pop($this->topics);
            } catch (Exception $exception) {
                $this->logger->warning(sprintf('polling queue exception: %s', $exception->getMessage()));
                continue;
            }

            if (!$data) {
                // 空轮询
                continue;
            }

            try {
                $this->delayQueue->validateClassName($data['className']);
            } catch(Exception $exception) {
                $this->logger->emergency($exception->getMessage());
                continue;
            }

            $this->perform($data);
        }
    }

    protected function perform(array $data)
    {
        $pid = pcntl_fork();
        if ($pid< 0) {
            $this->logger->emergency('Unable to fork child worker', ['job' => $data]);
            return;
        }
        if ($pid === 0) {
            // 子进程
            /** @var AbstractHandler $class */
            $class = new $data['className']($this->container);
            $class->setId($data['id']);
            $class->setBody($data['body']);
            $this->logger->info('Start processing Job', ['data' => $data]);
            $class->run();
            $this->logger->info('Job finished', ['data' => $data]);
            exit(0);
        }
        // 父进程
        $status = null;
        pcntl_wait($status);
        $exitStatus = pcntl_wexitstatus($status);
        if ($exitStatus !== 0) {
            // 执行失败
            $this->logger->warning('Job exited with exit code ' . $exitStatus);
        }
    }

    /**
     * 注册信号处理
     */
    protected function registerSignalHandlers()
    {
        pcntl_signal(SIGTERM, [$this, 'shutdown']);
        pcntl_signal(SIGINT , [$this, 'shutdown']);
    }

    /**
     * 无Job处理时退出
     */
    public function shutdown()
    {
        $this->logger->notice('Shutting down');
        $this->shutdown = true;
    }
}