<?php

namespace DelayQueue\Handler;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var string Job唯一标识
     */
    protected $id;

    /**
     * @var array
     */
    protected $body;

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param array $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    public function run()
    {
        $this->setUp();

        try {
            $this->perform();
        } catch (\Exception $exception) {

        }

        $this->tearDown();
    }

    protected function setUp() { }

    protected function tearDown() { }

    abstract protected function perform();
}