<?php
namespace api\controllers;

use Yii;

class SiteController extends BaseCotroller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionError()
    {
        echo 'error';
    }
}

