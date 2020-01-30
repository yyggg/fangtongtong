<?php
namespace api\controllers;

use Yii;
use yii\web\Controller;


class ExceptionController extends Controller
{
    public function actions()
    {
        return [
            'handler' => [
                'class' => 'yii\web\HttpException',
            ],
        ];
    }

    /*public function __construct($status, $message = null, $code = 0, \Exception $previous = null)
    {
        return parent::__construct($status, $message, $code, $previous);
    }

    public function actionHandler()
    {
        var_dump(222);die;
    }*/

}

