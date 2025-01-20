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
use yii\web\Response;

class DefaultController extends Controller
{
    private CityService $cityService;
    private TerminalService $terminalService;
    public $enableCsrfValidation = false;

    public function __construct($id, $module, $config = [])
    {
        $this->cityService = new CityService();
        $this->terminalService = new TerminalService();

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
                    'cities' => ['GET',],
                    'calc' => ['GET'],
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
    public function actionCities(): Response
    {
        try {
            $citiesData = $this->cityService->findCitiesByRequest(Yii::$app->request);
            $cities = (new CityDTO($citiesData))->getCitiesShort();
        } catch (Exception $exception) {
            return $this->asJson([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->asJson([
            'success' => true,
            'cities' => $cities,
        ]);
    }

    /**
     * @return Response
     */
    public function actionTerminals(): Response
    {
        try {
            $terminalsData = $this->terminalService->getTerminalsByRequest(Yii::$app->request);
            $terminals = (new TerminalDTO($terminalsData))->getTerminalsData();
        } catch (Exception $exception) {
            return $this->asJson([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->asJson([
            'success' => true,
            'terminals' => $terminals,
        ]);
    }
}
