<?php

namespace app\modules\api\controllers;

use app\modules\api\DTO\CityDTO;
use app\modules\api\DTO\TerminalDTO;
use app\modules\api\service\CityService;
use app\modules\api\service\TerminalService;
use Exception;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

class DefaultController extends Controller
{
    public $enableCsrfValidation = false;


    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'cities' => ['GET',],
                    'calc' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        return json_encode([
            'success' => false,
        ]);
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function actionTerminals(): string
    {
        try {
            $terminals = (new TerminalService())
                ->getTerminalsByRequest(Yii::$app->request);
        } catch (Exception $exception) {
            return json_encode([
                'success' => false,
                'error' => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'success' => true,
            'terminals' => (new TerminalDTO($terminals))->getTerminalsData(),
        ], JSON_UNESCAPED_UNICODE);
    }
}
