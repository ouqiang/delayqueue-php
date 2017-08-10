<?php


namespace DelayQueue;

use JsonSerializable;

/**
 * Job实体
 */
class Job implements JsonSerializable
{
    /**
     * @var string 队列名称
     */
    public $topic;

    /**
     * @var string Job唯一标识
     */
    public $id;

    /**
     * @var int 延迟时间, 单位秒
     */
    public $delay;

    /**
     * @var int 超时时间, 单位秒
     */
    public $ttr;

    /**
     * @var array Job内容
     */
    public $body;

    /**
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param string $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param int $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @return int
     */
    public function getTtr()
    {
        return $this->ttr;
    }

    /**
     * @param int $ttr
     */
    public function setTtr($ttr)
    {
        $this->ttr = $ttr;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param array $body
     */
    public function setBody(array $body)
    {
        $this->body = $body;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function appendValueToBody($key, $value)
    {
        $this->body[$key] = $value;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $arr = (array) $this;
        $arr['body'] = json_encode($arr['body']);

        return $arr;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}