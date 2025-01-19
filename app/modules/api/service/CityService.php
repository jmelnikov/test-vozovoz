<?php

namespace app\modules\api\service;

use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\redis\Connection;
use yii\web\Request;

class CityService
{
    private const CACHE_TTL = 3600;
    private Client $httpClient;
    private Connection $cache;


    public function __construct()
    {
        $this->httpClient = new Client();
        $this->cache = Yii::$app->{'cities-cache'};
    }

    /**
     * @throws \Exception
     */
    public function findCitiesByRequest(Request $request): array
    {

        $query = $request->get('query', '');
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        if (mb_strlen($query) < 3) {
            return [];
        }

        $params = [
            'search' => $query,
            'limit' => $limit,
            'offset' => $offset,
        ];

        $cacheKey = implode('|', $params);
        $response = $this->cache->get($cacheKey);

        if (!$response) {
            $response = $this->getCitiesFromServer($params);

            $this->cache->set($cacheKey, $response);
            $this->cache->expire($cacheKey, self::CACHE_TTL);
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
