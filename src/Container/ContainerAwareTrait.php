<?php


namespace DelayQueue\Container;


use DelayQueue\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use DelayQueue\DelayQueue;


/**
 *
 * @property LoggerInterface $logger
 * @property DelayQueue      $delayQueue
 */
trait ContainerAwareTrait
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __get($id)
    {
        return $this->container->get($id);
    }
}