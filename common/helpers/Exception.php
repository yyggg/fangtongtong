<?php

namespace  common\helpers;

use yii\web\ErrorHandler;
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-16 19:11
 */

class Exception extends ErrorHandler
{
    public function renderException($exception)
    {
        $data = [
            'code' => 500,
            'msg' => $exception->getMessage(),
            'data' => [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]
        ];
        die(json_encode($data));
    }
}
