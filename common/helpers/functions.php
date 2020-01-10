<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-02 15:05
 */

if (! function_exists('response'))
{
    function response($data = [], $code = '0')
    {

        if (!$data && $code != '0')
        {
            return [
                'errCode' => Yii::$app->params['errCode'][$code]['errCode'],
                'errMsg' => Yii::$app->params['errCode'][$code]['errMsg']];
        }

        return [
            'errCode' => Yii::$app->params['errCode'][$code]['errCode'],
            'data' => $data
        ];

    }
}
