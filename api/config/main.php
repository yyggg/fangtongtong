<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ]
    ],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'enableCsrfValidation' => false,
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
//                $code = $response->getStatusCode();
//                $msg = $response->statusText;
//                if ($code == 404) {
//                    !empty($response->data['message']) && $msg = $response->data['message'];
//                }
                $response->format = yii\web\Response::FORMAT_JSON;
            },
        ],

        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
            //'class' => 'common\helpers\Exception' //此处新增
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,

        ],
    ],
    'params' => $params,
];
