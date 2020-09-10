<?php
return [
    'language' => 'ru-RU',
    'timeZone' => 'Europe/Moscow',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@backend/runtime/cache',
            'dirMode' => 0777,
            'fileMode' => 0777,
        ],
        'formatter' => [
            'timeZone' => 'Europe/Moscow',
            'defaultTimeZone' => 'Europe/Moscow',
        ],
        'response' => [
            'class' => \yii\web\Response::class,
            'on beforeSend' => function ($event) {
                // только в режиме dev даём возможность просмтривать меню yii
                !YII_ENV_DEV ? $event->sender->headers->add('X-Frame-Options', 'DENY') : null;
                $event->sender->headers->add('X-Content-Type-Options', 'nosniff');
                $event->sender->headers->add('X-XSS-Protection', '1; mode=block');
                $event->sender->headers->add(
                    'Content-Security-Policy',
                    "default-src 'none';"
                    . "script-src 'self' 'unsafe-eval' 'unsafe-inline' http://www.google.com https://www.gstatic.com;"
                    . "style-src 'self' 'unsafe-inline';"
                    . "img-src 'self' data: blob:;"
                    . "object-src 'self' data: blob:;"
                    . "connect-src 'self' http://www.google.com;"
                    . "frame-src 'self' http://www.google.com;"
                    . "manifest-src 'self';"
                    . "font-src 'self' data:"
                );
            },
        ],
    ],
];
