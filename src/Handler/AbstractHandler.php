<?php

namespace DelayQueue\Handler;

abstract class AbstractHandler implements HandlerInterface
{
    protected $body;

    public function run()
    {
        $this->setUp();

        $this->perform();

        $this->tearDown();
    }

    protected function setUp() { }

    protected function tearDown() { }

    abstract protected function perform();
}