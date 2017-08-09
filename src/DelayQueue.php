<?php

namespace DelayQueue;

use DelayQueue\Exception\ClassNotFoundException;
use DelayQueue\Exception\SubClassException;
use GuzzleHttp\Client as HttpClient;
use ReflectionClass;
use DelayQueue\Handler\AbstractHandler;

class DelayQueue
{
    /**
     * @var HttpClient
     */
    protected $httpClient;
    /**
     * @var string 延迟队列服务器地址 http://example.com:9277
     */
    protected $server;
    /**
     * @var int  长轮询队列, 超时时间
     */
    protected $pollingTimeout = 180;

    public function __construct($server)
    {
        $this->server = rtrim($server, '/');

        $this->initHttpClient();
    }

    /**
     * @param int $pollingTimeout
     */
    public function setPollingTimeout($pollingTimeout)
    {
        $this->pollingTimeout = $pollingTimeout;
    }

    /**
     * 添加Job到延迟队列中
     *
     * @param string $className 处理Job的类名, 必须是[DelayQueue\Handler\AbstractHandler]的子类
     * @param Job    $job
     */
    public function push($className, Job $job)
    {
        if (!class_exists($className)) {
            throw new ClassNotFoundException(sprintf('can not find class [%s]', $className));
        }
        $reflection = new ReflectionClass($className);
        if (!$reflection->isSubclassOf(AbstractHandler::class)) {
            throw new SubClassException(sprintf('[%s] is not subclass of [%s]', $className, AbstractHandler::class));
        }

        $body = $job->getBody();
        $body['className'] = $className;
        $job->setBody($body);

        $response = $this->httpClient->post('/push', [
            'json' => $job->toArray(),
        ]);
        return $response->json();
    }

    /**
     * @param array $topics 队列名称
     */
    public function pop(array $topics)
    {
        $response = $this->httpClient->post('/pop', [
            'json' => [
                'topic' => implode(',', $topics),
            ]
        ]);

        return $response->json();
    }

    /**
     * @param  string $id Job唯一标识
     */
    public function delete($id)
    {
        $response = $this->httpClient->post('/delete', [
            'json' => [
               'id' => $id
            ]
        ]);
        return $response->json();
    }

    /**
     * @param  string $id Job唯一标识
     */
    public function finish($id)
    {
        $response = $this->httpClient->post('/finish', [
            'json' => [
                'id' => $id,
            ]
        ]);
        return $response->json();
    }

    protected function initHttpClient()
    {
        $this->httpClient = new HttpClient(
            [
                'base_url' => $this->server,
                'defaults' => [
                    'timeout' => $this->pollingTimeout + 20,
                    'allow_redirects' => false,
                ]
            ]
        );
    }
}