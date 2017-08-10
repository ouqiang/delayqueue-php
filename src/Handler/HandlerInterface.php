<?php

namespace DelayQueue\Handler;

/**
 * Job处理接口
 *
 * Interface HandlerInterface
 * @package DelayQueue\Handler
 */
interface HandlerInterface
{
    public function run();
}