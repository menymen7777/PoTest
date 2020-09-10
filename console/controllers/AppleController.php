<?php

namespace console\controllers;

use common\models\fruit\Apple;
use common\services\fruit\AppleService;
use yii\console\Controller;
use yii\db\Expression;

/**
 * Class AppleController
 * @package console\controllers
 */
class AppleController extends Controller
{
    /**
     * Гниение яблок
     */
    public function actionCheckFresh()
    {
        $nonFreshAppleQuery = Apple::find()
            ->andWhere(['status' => Apple::STATUS_DROPPED])
            ->andWhere(['is not', 'dropped_at', new Expression('null')])
            ->andWhere(['<=', 'dropped_at', date('Y-m-d H:i:s', strtotime('-' . Apple::ROTTED_AWAY_AFTER . 'sec'))]);

        $total = 0;
        $error = 0;
        foreach ($nonFreshAppleQuery->each() as $apple) {
            try {
                $total++;
                $service = new AppleService($apple);
                $service->rottedAway();
            } catch (\Exception $e) {
                $error++;
            }
        }

        if ($total) {
            echo "Сгнило $total яблок. Ошибок: $error\n";
        }
    }

    /**
     * Гниение яблок
     */
    public function actionCheckRottedAway()
    {
        $rottedAwayQuery = Apple::find()
            ->andWhere(['status' => Apple::STATUS_ROTTED_AWAY])
            ->andWhere(['is not', 'rotted_away_at', new Expression('null')])
            ->andWhere(['<=', 'rotted_away_at', date('Y-m-d H:i:s', strtotime('-' . Apple::DECAY_AFTER . 'sec'))]);

        $total = 0;
        $error = 0;
        foreach ($rottedAwayQuery->each() as $apple) {
            try {
                $total++;
                $service = new AppleService($apple);
                $service->decay();
            } catch (\Exception $e) {
                $error++;
            }
        }

        if ($total) {
            echo "Разложилось $total яблок. Ошибок: $error\n";
        }
    }
}
