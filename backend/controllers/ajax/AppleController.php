<?php

namespace backend\controllers\ajax;

use backend\forms\apple\AppleEatForm;
use common\models\fruit\Apple;
use common\services\fruit\AppleService;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class AppleController
 * @package backend\controllers\ajax
 */
class AppleController extends Controller
{
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
                        'actions' => ['drop', 'trash', 'get-eat-modal'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'drop' => ['post'],
                    'trash' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return true;
        }

        return false;
    }

    /**
     * Срывание яблок
     * @param $id
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\UserException
     */
    public function actionDrop($id)
    {
        $apple = $this->findApple($id);
        $service = new AppleService($apple);
        $service->drop();

        return true;
    }

    /**
     * Выкидывание яблок
     * @param $id
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\base\UserException
     * @throws \yii\db\StaleObjectException
     */
    public function actionTrash($id)
    {
        $apple = $this->findApple($id);
        $service = new AppleService($apple);
        $service->trash();

        return true;
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws UserException
     */
    public function actionGetEatModal($id)
    {
        $apple = $this->findApple($id);
        if (!$apple->canEat()) {
            throw new UserException('Вы не можете съесть яблоко');
        }

        $modelForm = new AppleEatForm();
        return $this->renderAjax('@backend/views/apple/_eat_modal', [
            'modelForm' => $modelForm,
            'model' => $apple,
        ]);
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
