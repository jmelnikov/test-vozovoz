<?php

namespace app\modules\api\controllers;

use app\modules\api\CitiesDTO;
use app\modules\api\service\CitiesService;
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
            $cities = (new CitiesService())
                ->findCitiesByQuery(
                    Yii::$app->request->post('query', ''),
                    Yii::$app->request->post('limit', 20),
                    Yii::$app->request->post('offset', 0)
                );
        } catch (Exception $exception) {
            return json_encode([
                'success' => false,
                'error' => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'success' => true,
            'cities' => (new CitiesDTO($cities))->getCitiesShort(),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function actionCalc(): string
    {
        return '{"success": true}';
    }
}
