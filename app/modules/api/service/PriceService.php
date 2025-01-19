<?php

namespace app\modules\api\service;

use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\redis\Connection;
use yii\web\Request;

class PriceService
{
    private const CACHE_TTL = 3600;
    private Client $httpClient;
    private Connection $cache;


    public function __construct()
    {
        $this->httpClient = new Client();
        $this->cache = Yii::$app->{'prices-cache'};
    }

    /**
     * @throws Exception
     */
    public function getPricesByFormRequest(Request $request): array
    {
        $deliveryFrom = $request->get('deliveryFrom', 'г Москва');
        $deliveryTo = $request->get('deliveryTo', 'г Санкт-Петербург');
        $radioFrom = $request->get('radioFrom', 'terminal');
        $radioTo = $request->get('radioTo', 'terminal');
        $volume = $request->get('volume', 0.1);
        $weight = $request->get('weight', 0.1);
        $quantity = $request->get('quantity', 1);

        if ($radioFrom == 'terminal') {
            $dispatch = [
                'point' => [
                    'location' => $deliveryFrom,
                    'terminal' => $request->get('terminalFrom', 'default')
                ]
            ];
        } else {
            $dispatch = [
                'point' => [
                    'location' => $deliveryFrom
                ]
            ];
        }

        if ($radioTo == 'terminal') {
            $destination = [
                'point' => [
                    'location' => $deliveryTo,
                    'terminal' => $request->get('terminalTo', 'default')
                ]
            ];
        } else {
            $destination = [
                'point' => [
                    'location' => $deliveryTo
                ]
            ];
        }

        $params = [
            'cargo' => [
                'dimension' => [
                    'quantity' => $quantity,
                    'volume' => $volume,
                    'weight' => $weight,
                ]
            ],
            'gateway' => [
                'dispatch' => $dispatch,
                'destination' => $destination
            ]
        ];

        $cacheKey = md5(serialize($params));
        $response = $this->cache->get($cacheKey);

        if (!$response) {
            $response = $this->getPricesFromServer($params);

            $this->cache->set($cacheKey, $response);
            $this->cache->expire($cacheKey, self::CACHE_TTL);
        }

        return json_decode($response, true);
    }

    /**
     * @throws Exception
     */
    private function getPricesFromServer(array $params): string
    {
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl(env('API_URL'))
                ->setData([
                    'object' => 'price',
                    'action' => 'get',
                    'params' => $params,
                ])
                ->send();
        } catch (InvalidConfigException|Exception $exception) {
            throw new Exception('Ошибка при поиске города: ' . $exception->getMessage(), 500, $exception);
        }

        // Возвращаем только города, без дополнительной информации
        return json_encode($response->getData()['response']);
    }
}
