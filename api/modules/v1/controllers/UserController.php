<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-09 17:31
 */

namespace api\modules\v1\controllers;

use api\models\SignupForm;
use api\models\User;
use common\models\InviteReg;
use common\models\PropertiesAnswers;
use common\models\UserArticle;
use Yii;
use api\controllers\BaseController;

class UserController extends BaseController
{

    /**
     * 我的 接口
     * @return array
     */
    public function actionIndex()
    {
        $response = [];
        $response['art_num'] = UserArticle::find()
            ->where(['user_id' => $this->_userId])
            ->count(1);
        $response['ask_num'] = PropertiesAnswers::find()
            ->where(["answers_user_id" => $this->_userId])
            ->count(1);
        $response['user'] = User::find()
            ->select(['user_id', 'is_adviser'])
            ->where(["user_id" => $this->_userId])
            ->asArray()
            ->one();
        return response($response);
    }

    /**
     * 个人信息
     * @return array
     */
    public function actionInfo()
    {
        $user = User::find()
            ->where(['user_id' => $this->_userId])
            ->asArray()
            ->one();

        if ($user['password'])
        {
            $user['password'] = '***';
        }

        $user['intive_code'] = User::createInviteCode($this->_userId);

        return response($user);
    }

    /**
     * 登录接口
     * @return array
     * @throws \yii\base\Exception
     */
    public function actionLogin()
    {
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
            $code = Yii::$app->redis->get('sms:'.$phone . '_1');
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
        $inviteCode  = Yii::$app->request->post('invite_code', 0);
        $source  = Yii::$app->request->post('source', 0);
        $sourceId  = Yii::$app->request->post('source_id', 0);

        if ($smsCode != Yii::$app->redis->get('sms:' . $phone . '_0'))
        {
            return response([], '30030', '验证码不正确。');
        }
        $data = [
            'SignupForm' => [
                'phone' => $phone,
                'password' => $password,
                'sms_code' => $smsCode,
                'invite_code' => $inviteCode,
                'source' => $source,
                'source_id' => $sourceId,
            ]
        ];

        $model = new SignupForm();

        if ($model->load($data) && $model->signup())
        {
            return response();
        }

        return response([], '30030', $model->getErrorSummary(false)[0]);
    }

    /**
     * 发手机验证码
     * @return array
     */
    public function actionSendSms()
    {
        $phone = Yii::$app->request->get('phone', '');
        $type = Yii::$app->request->get('type', 0);

        $res = sendSms($phone, '', $type);
        if ($res)
        {
            return response();
        }
        return response([], '30030', '发送手机验证码失败。');
    }

    /**
     * 修改手机号
     * @return array
     */
    public function actionUpdatePhone()
    {
        $userId = $this->_userId;

        $phone = Yii::$app->request->post('phone', '');
        $code = Yii::$app->request->post('sms_code', '');

        if (!$code || !$phone)
        {
            return response([], '20003');
        }

        $redisKey = 'sms:' . $phone . '_2';

        $model = User::findOne(['phone' => $phone]);
        if ($model)
        {
            return response([], '30030', '手机号已被占用。');
        }

        if ($code != Yii::$app->redis->get($redisKey))
        {
            return response([], '30030', '手机验证码错误。');
        }

        $model = User::findOne(['user_id' => $userId]);
        $model->phone = $phone;
        if ($model->save())
        {
            return response();
        }

        return response([], '20001');
    }

    // 头像修改
    public function actionUpdateAvatar()
    {
        $model = User::findOne(['user_id' => $this->_userId]);
        $avatar = uploads('avatar');

        if ($avatar)
        {
            $model->headimgurl = $avatar;
            $res = $model->save(false);
            if ($res)
            {
                return response();
            }
        }
        return response([], '20001');
    }

    /**
     * 修改用户信息
     * @return array
     */
    public function actionUpdateField()
    {
        $userId = $this->_userId;

        $param = Yii::$app->request->post();
        //允许修改字段
        $allow = ['nickname', 'sex', 'profession'];

        $model = User::findOne(['user_id' => $userId]);

        foreach ($param as $k => $v)
        {
            if (!in_array($k, $allow))
            {
                continue;
            }
            $model->$k = $v;
        }

        if ($model->save())
        {
            return response();
        }

        return response([], '20001');

    }

    /**
     * 修改密码
     * @return array
     * @throws \yii\base\Exception
     */
    public function actionUpdatePassword()
    {
        $oldPass = Yii::$app->request->post('old_password', '');
        $pass = Yii::$app->request->post('password', '');

        $model = User::findOne(['user_id' => $this->_userId]);

        if (!Yii::$app->security->validatePassword($oldPass, $model->password))
        {
            return response([], '30030', '旧密码不正确。');
        }

        $model->password = User::setPassword($pass);
        if ($model->save(false))
        {
            return response();
        }
        return response([], '20001');
    }

    /**
     * 修改邀请码
     * @return array
     */
    public function actionUpdateInviteCode()
    {
        $inviteCode = Yii::$app->request->post('invite_code', '');

        if ($inviteCode)
        {
            $userId = User::inviteDecode($inviteCode);
            $model = InviteReg::findOne(['reg_user_id' => $this->_userId, 'invite_user_id' => $userId]);
            if ($model)
            {
                return response();
            }

            InviteReg::createLevelRelation($userId, $this->_userId, 0, 0);
            return response();
        }

        return response([], '20001');
    }

    /**
     * 获取邀请码
     */
    public function actionInviteCode()
    {
        $response = ['invite_code' => User::createInviteCode($this->_userId)];
        response($response);
    }

}
