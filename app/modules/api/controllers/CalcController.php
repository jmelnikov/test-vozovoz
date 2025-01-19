<?php

namespace app\modules\api\controllers;

use app\modules\api\service\PriceService;
use Exception;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

class CalcController extends Controller
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
                    'form' => ['GET',],
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
    public function actionForm(): string
    {
        try {
            $price = (new PriceService())->getPricesByFormRequest(Yii::$app->request);
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
