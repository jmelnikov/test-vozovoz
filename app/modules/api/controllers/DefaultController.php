<?php

namespace app\modules\api\controllers;

use app\modules\api\DTO\CityDTO;
use app\modules\api\service\CityService;
use app\modules\api\service\PriceService;
use Exception;
use Yii;
use yii\web\Controller;

class DefaultController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex(): string
    {
        return '{"success": true}';
    }

    public function actionCities(): string
    {
        try {
            $cities = (new CityService())
                ->findCitiesByRequest(Yii::$app->request);
        } catch (Exception $exception) {
            return json_encode([
                'success' => false,
                'error' => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'success' => true,
            'cities' => (new CityDTO($cities))->getCitiesShort(),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function actionCalc(): string
    {
        try {
            $price = (new PriceService())->getPricesByRequest(Yii::$app->request);
        } catch (Exception $exception) {
            return json_encode([
                'success' => false,
                'error' => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'success' => true,
            'price' => $price,
        ], JSON_UNESCAPED_UNICODE);
    }
}
