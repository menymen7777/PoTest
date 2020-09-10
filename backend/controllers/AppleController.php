<?php

namespace backend\controllers;

use backend\forms\apple\AppleEatForm;
use backend\models\AppleSearch;
use backend\traits\BaseExceptionProcessingErrorsTrait;
use common\models\fruit\Apple;
use common\services\fruit\AppleService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;
use yii\widgets\ActiveForm;

/**
 * Class AppleController
 * @package backend\controllers
 */
class AppleController extends Controller
{
    use BaseExceptionProcessingErrorsTrait;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'generate', 'eat'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'generate' => ['post'],
                    'eat' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AppleSearch();
        $apples = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'apples' => $apples,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * Генерация яблок
     * @return \yii\web\Response
     */
    public function actionGenerate()
    {
        try {
            $amountGenerated = AppleService::generateApples();
            \Yii::$app->session->setFlash('success', "Генерация прошла успещно, было создано яблок: $amountGenerated");
        } catch (\Exception $e) {
            $this->processingException($e, $message);
            $this->setMessage($message);
        }

        return $this->redirect('/apple');
    }

    /**
     * Откусывание яблока
     * @param $id
     * @return array|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionEat($id)
    {
        $apple = $this->findApple($id);
        $modelForm = new AppleEatForm();
        $modelForm->sizeBefore = round($apple->size * 100);
        if (Yii::$app->request->isAjax && $modelForm->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modelForm);
        }

        if ($modelForm->load(\Yii::$app->request->post())) {
            $modelForm->size = ($modelForm->sizeBefore - $modelForm->size) / 100;
            try {
                $service = new AppleService($apple);
                $service->eat($modelForm->attributes);
                Yii::$app->session->setFlash('success', 'Яблоко успешно съедено');
            } catch (\Exception $e) {
                $this->processingException($e, $message);
                $this->setMessage($message);
            }
        }

        return $this->redirect('/apple');
    }

    /**
     * @param $id
     * @return array|Apple|\yii\db\ActiveRecord|null
     * @throws NotFoundHttpException
     */
    protected function findApple($id)
    {
        $apple = Apple::find()
            ->andWhere(['id' => (int) $id])
            ->limit(1)
            ->one();

        if (!$apple) {
            throw new NotFoundHttpException('Яблоко не найдено');
        }

        return $apple;
    }
}
