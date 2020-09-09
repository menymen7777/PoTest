<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m200909_134102_create_admin
 */
class m200909_134102_create_admin extends Migration
{
    /**
     * {@inheritdoc}
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->batchInsert(
            '{{%user.user}}',
            [
                'username',
                'auth_key',
                'password_hash',
                'email',
                'status'
            ],
            [
                [
                    'admin',
                    Yii::$app->security->generateRandomString(32),
                    Yii::$app->security->generatePasswordHash('password'),
                    'admin@mysite.ru',
                    User::STATUS_ACTIVE
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%user.user}}', ['email' => 'admin@mysite.ru']);
    }
}
