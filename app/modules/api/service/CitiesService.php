<?php

namespace app\modules\api\service;

use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\redis\Connection;

class CitiesService
{
    private const CACHE_TTL = 3600;

    private Client $httpClient;
    private Connection $redis;


    public function __construct()
    {
        $this->httpClient = new Client();
        $this->redis = \Yii::$app->{'cities-cache'};
    }

    /**
     * @throws \Exception
     */
    public function findCitiesByQuery(string $query, int $limit = 20, int $offset = 0)
    {
        if (mb_strlen($query) < 3) {
            return [];
        }

        $params = [
            'search' => $query,
            'limit' => $limit,
            'offset' => $offset,
        ];

        $response = $this->redis->get(implode('|', $params));

        if (!$response) {
            $response = $this->getCitiesFromServer($params);

            $this->redis->set(implode('|', $params), $response);
            $this->redis->expire(implode('|', $params), self::CACHE_TTL);
        }

        return json_decode($response, true);
    }

    /**
     * @throws Exception
     */
    private function getCitiesFromServer(array $params): string
    {
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl(env('API_URL'))
                ->setData([
                    'object' => 'location',
                    'action' => 'get',
                    'params' => $params,
                ])
                ->send();
        } catch (InvalidConfigException|Exception $exception) {
            throw new Exception('Ошибка при поиске города: ' . $exception->getMessage(), 500, $exception);
        }

        // Возвращаем только города, без дополнительной информации
        return json_encode($response->getData()['response']['data']);
    }
}
