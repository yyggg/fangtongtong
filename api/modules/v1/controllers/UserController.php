<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-09 17:31
 */

namespace api\modules\v1\controllers;

use api\models\SignupForm;
use api\models\User;
use Yii;
use api\controllers\BaseCotroller;

class UserController extends BaseCotroller
{
    /**
     * 登录接口
     * @return array
     * @throws \yii\base\Exception
     */
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
            $code = Yii::$app->redis->get($phone);
            if ($code != $smsCode)
            {
                return response([], '30100');
            }
            $model = User::findByPhone($phone);
        }


        if (!$model)
        {
            return response([], '30100');
        }

        $model['access_token'] = User::generateAccessToken($model['user_id']);

        return response($model);

    }

    /**
     * 注册
     * @return array
     */
    public function actionSignup()
    {
        $phone    = Yii::$app->request->post('phone', '');
        $password = Yii::$app->request->post('password', '');
        $smsCode  = Yii::$app->request->post('sms_code', '');

        $data = [
            'SignupForm' => [
                'phone' => $phone,
                'password' => $password,
                'sms_code' => $smsCode,
            ]
        ];

        $model = new SignupForm();

        if ($model->load($data) && $model->signup())
        {
            return response();
        }

        return response([], '30030', $model->getErrorSummary(false)[0]);
    }

}
