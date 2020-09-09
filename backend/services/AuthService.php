<?php

namespace backend\services;

use backend\forms\auth\LoginForm;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\UserException;
use Yii;

/**
 * Class AuthService
 * @package backend\services
 */
class AuthService extends BaseObject
{
    /**
     * Авторизация
     * @param LoginForm $form
     * @throws Exception
     * @throws UserException
     */
    public function signIn(LoginForm $form)
    {
        if (!$form->validate()) {
            throw new UserException(implode(PHP_EOL, $form->firstErrors));
        }

        if (!Yii::$app->user->login($form->getUser(), $form->rememberMe ? 3600 * 24 * 30 : 0)) {
            throw new Exception('Ошибка входа');
        }
    }
}
