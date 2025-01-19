<?php

namespace app\modules\api\controllers;

use app\modules\api\service\PriceService;
use Exception;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class CalcController extends Controller
{
    private PriceService $priceService;

    public $enableCsrfValidation = false;

    public function __construct($id, $module, $config = [])
    {
        $this->priceService = new PriceService();

        parent::__construct($id, $module, $config);
    }

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
     * @return Response
     */
    public function actionIndex(): Response
    {
        return $this->asJson([
            'success' => false,
            'message' => 'Method not implemented',
        ]);
    }

    /**
     * @return Response
     */
    public function actionForm(): Response
    {
        try {
            $price = $this->priceService->getPricesByFormRequest(Yii::$app->request);
        } catch (Exception $exception) {
            return $this->asJson([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->asJson([
            'success' => true,
            'price' => $price,
        ]);
    }
}
