<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-09 17:31
 */

namespace api\modules\v1\controllers;

use api\models\User;
use Yii;
use api\controllers\BaseCotroller;

class UserController extends BaseCotroller
{
    public function actionLogin()
    {
        $data = [];
        $phone = Yii::$app->request->post('phone', '');
        $smsCode = Yii::$app->request->post('sms_code', '');
        $password = Yii::$app->request->post('password', '');

        // 判断登录方式是密码还是手机验证码
        if ($password)
        {
            $model = User::findByPassword($phone, $password);
        }
        else
        {

        }



        if (!$model)
        {
            return response([], '30100');
        }

        $model['access_token'] = User::generateAccessToken($model['user_id']);


        return response($model);

    }

}
