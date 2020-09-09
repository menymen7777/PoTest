<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=localhost;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'schemaCache' => 'cache',
            'on afterOpen' => function ($event) {
                $tz = date_default_timezone_get();  // Получаем текущую временную зону
                Yii::$app->db->createCommand("SET TIME ZONE '$tz'")->execute(); // Устанавливаем зону в БД
                return true;
            },
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
