<?php

namespace backend\traits;

use Yii;
use yii\base\UserException;

/**
 * Class BaseExceptionProcessingErrorsTrait
 * @package quartz\tools\modules\errorHandler\traits
 */
trait BaseExceptionProcessingErrorsTrait
{
    /**
     * @param \Exception $exception
     * @param string $messageForUser
     * @param string $messageServer
     */
    protected function processingException(
        \Exception $exception,
        ?string &$messageForUser = '',
        ?string &$messageServer = ''
    ) {
        $messageForUser = '';
        $messageServer = $exception->getMessage();
        if ($exception instanceof UserException) {
            $messageForUser = $exception->getMessage();
        }  else {
            $messageForUser = 'Внутрення ошибка сервера';
        }

        Yii::error($messageServer);
    }

    /**
     * Устанавливаем сообщение
     * @param $messageForUser
     * @param string $category
     * @return string|array
     */
    protected function setMessage($messageForUser, string $category = 'error')
    {
        $message = '';

        if (Yii::$app->request instanceof \yii\console\Request) {
            $message = $messageForUser;
        } elseif (!Yii::$app->request->isAjax) {
            Yii::$app->session->addFlash($category, $messageForUser);
        } else {
            $message = [
                'type' => 'success',
                'message' => $messageForUser,
            ];
        }

        return $message;
    }
}
