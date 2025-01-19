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
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function getPricesByFormRequest(Request $request): array
    {
        $deliveryFrom = $request->get('deliveryFrom', 'г Москва');
        $terminalFrom = $request->get('terminalFrom', 'default');
        $deliveryTo = $request->get('deliveryTo', 'г Санкт-Петербург');
        $terminalTo = $request->get('terminalTo', 'default');
        $radioFrom = $request->get('radioFrom', 'terminal');
        $radioTo = $request->get('radioTo', 'terminal');
        $volume = $request->get('volume', 0.1);
        $weight = $request->get('weight', 0.1);
        $quantity = $request->get('quantity', 1);

        $dispatch = $this->getDestination($radioFrom, $deliveryFrom, $terminalFrom);
        $destination = $this->getDestination($radioTo, $deliveryTo, $terminalTo);

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
     * @param string $type
     * @param string $location
     * @param string|null $terminal
     * @return array[]
     */
    private function getDestination(string $type, string $location, ?string $terminal = null): array
    {
        if ($type == 'terminal') {
            return [
                'point' => [
                    'location' => $location,
                    'terminal' => $terminal
                ]
            ];
        } else {
            return [
                'point' => [
                    'location' => $location,
                ]
            ];
        }
    }

    /**
     * @param array $params
     * @return string
     * @throws Exception
     */
    private function getPricesFromServer(array $params): string
    {
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl(Yii::$app->params['apiUrl'])
                ->setData([
                    'object' => 'price',
                    'action' => 'get',
                    'params' => $params,
                ])
                ->send();
        } catch (InvalidConfigException|Exception $exception) {
            throw new Exception('Ошибка при поиске города: ' . $exception->getMessage(), 500, $exception);
        }

        if (!empty($response->getData()['error'])) {
            throw new Exception($response->getData()['error']['message']);
        }

        return json_encode($response->getData()['response']);
    }
}
