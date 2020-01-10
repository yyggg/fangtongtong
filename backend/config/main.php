<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
	'name' => '后台管理系统',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        "rbac" => [
            'class' => 'rbac\Module',
        ],
        "system" => [
            'class' => 'system\Module',
        ],
        "orders" => [
            'class' => 'orders\Module',
        ],
        "article" => [
            'class' => 'article\Module',
        ],
        "goods" => [
            'class' => 'goods\Module',
        ],
        "carousel" => [
            'class' => 'carousel\Module',
        ],
        "single" => [
            'class' => 'single\Module',
        ],
        "feedback" => [
            'class' => 'feedback\Module',
        ],
        "backup" => [
            'class' => 'backup\Module',
        ],
    ],
    "aliases" => [
        '@rbac' => '@backend/modules/rbac',
		'@system' => '@backend/modules/system',
		'@orders' => '@backend/modules/orders',
		'@article' => '@backend/modules/article',
		'@goods' => '@backend/modules/goods',
		'@carousel' => '@backend/modules/carousel',
		'@single' => '@backend/modules/single',
		'@feedback' => '@backend/modules/feedback',
		'@backup' => '@backend/modules/backup',
    ],
    'components' => [
        'excel' => [
            'class' => 'illusion\excel\Spreadsheet',
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
		'assetManager' => [
			'bundles' => [
//				'yii\web\YiiAsset' => [
//					'js' => [],  // 去除 yii.js
//					'sourcePath' => null,  // 防止在 frontend/web/asset 下生产文件
//				],
//				'yii\widgets\ActiveFormAsset' => [
//					'js' => [],  // 去除 yii.activeForm.js
//					'sourcePath' => null,  // 防止在 frontend/web/asset 下生产文件
//				],
//				'yii\validators\ValidationAsset' => [
//					'js' => [],  // 去除 yii.validation.js
//					'sourcePath' => null,  // 防止在 frontend/web/asset 下生产文件
//				],
//				'yii\web\JqueryAsset' => [
//					'js' => [],  // 去除 jquery.js
//					'sourcePath' => null,  // 防止在 frontend/web/asset 下生产文件
//				],
//				'yii\bootstrap\BootstrapAsset' => [
//					'css' => [],  // 去除 bootstrap.css
//					'sourcePath' => null, // 防止在 frontend/web/asset 下生产文件
//				],
//				'yii\bootstrap\BootstrapPluginAsset' => [
//					'js' => [],  // 去除 bootstrap.js
//					'sourcePath' => null,  // 防止在 frontend/web/asset 下生产文件
//				],
			],
		],
        'user' => [
            'identityClass' => 'rbac\models\User',
            'loginUrl' => array('/rbac/user/login'),
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-backend',
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
        ],
        "authManager" => [
            "class" => 'yii\rbac\DbManager', //这里记得用单引号而不是双引号
            "defaultRoles" => ["guest"],
        ],
        "urlManager" => [
            "enablePrettyUrl" => true,
            "enableStrictParsing" => false,
            "showScriptName" => false,
            "suffix" => "",
            "rules" => [
                "<controller:\w+>/<id:\d+>"=>"<controller>/view",
                "<controller:\w+>/<action:\w+>"=>"<controller>/<action>"
            ],
        ],
        "formatter" => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
        ]
    ],
    'as access' => [
        'class' => 'rbac\components\AccessControl',
        'allowActions' => [
            'rbac/user/request-password-reset',
            'rbac/user/reset-password',
			'*'
        ]
    ],
    'params' => $params,
];
