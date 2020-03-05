<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-09 17:31
 */

namespace api\modules\v1\controllers;

use api\models\SignupForm;
use api\models\User;
use common\models\HouseType;
use common\models\InviteReg;
use common\models\Properties;
use common\models\PropertiesAdviserRelation;
use common\models\PropertiesAnswers;
use common\models\PropertiesLabel;
use common\models\PropertiesLabelRelation;
use common\models\Region;
use common\models\UserArticle;
use common\models\UserDistribution;
use crazyfd\qiniu\Qiniu;
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
        $type = Yii::$app->request->post('type', 'pass'); // 登录类型 sms短信, pass密码

        // 判断登录方式是密码还是手机验证码
        if ($type === 'pass')
        {
            if (!$password || $password == null)
            {
                return response([], '30030', '密码不能为空。');
            }
            $model = User::findByPassword($phone);
            if (!$model)
            {
                return response([], '30030', '用户不存在。');
            }

            if (!Yii::$app->security->validatePassword($password, $model['password']))
            {
                return response([], '30030', '密码错误。');
            }
        }
        else
        {
            $code = Yii::$app->redis->get('sms:'.$phone . '_1');

            if (!$code || $code != $smsCode)
            {
                return response([], '30030', '手机验证码错误');
            }
            $model = User::findByPhone($phone);
            if (!$model)
            {
                return response([], '30030', '用户不存在。');
            }
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
                'source_id' => $sourceId
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
        if (!$res['code'])
        {
            return response();
        }
        return response([], '30030', $res['msg']);
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
        $fileName = uniqid().time();

        $model = User::findOne(['user_id' => $this->_userId]);

        $qiniu = new Qiniu(Yii::$app->params['access_key'],Yii::$app->params['secret_key'],Yii::$app->params['domain'],Yii::$app->params['bucket']);

        $qiniu->uploadFile($_FILES['avatar']['tmp_name'], $fileName);

        $url = $qiniu->getLink($fileName);

        if ($url)
        {
            $model->headimgurl = $url;
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
        return response($response);
    }

    /**
     * 我的分销下级
     * @return array
     */
    public function actionDownLevel()
    {
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = InviteReg::find()
            ->alias('a')
            ->select([
                'b.user_id',
                'b.headimgurl',
                'b.nickname',
                'b.create_time',
                'b.phone'
            ])
            ->where([
                'invite_user_id' => $this->_userId
            ])
            ->leftJoin(User::tableName() . ' b', 'a.reg_user_id = b.user_id')
            ->orderBy('invite_reg_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['phone'] = substr_replace($v['phone'], '****', 3, 4);
            $v['create_time'] = date('Y.m.d', $v['create_time']);
            $model[$k] = $v;
        }

        return response($model);
    }

    /**
     * 我的分销信息
     * @return array
     */
    public function actionDistribution()
    {
        $page          = Yii::$app->request->post('page', 1); // 页码
        $offset = ($page - 1) * \Yii::$app->params['pageSize'];

        $model = UserDistribution::find()
            ->alias('f')
            ->select([
                'f.commission','f.register_time','f.sale_time','a.properties_id','a.name','a.pic','a.price_metre',
                'a.sale_status','MIN(e.square_metre) AS square_metre_min', 'MAX(e.square_metre) AS square_metre_max',
                'GROUP_CONCAT(DISTINCT c.label_name) AS label_name','MAX(d.region_name) AS region_name'
            ])
            ->leftJoin(Properties::tableName() . ' a', 'f.properties_id=a.properties_id')
            ->leftJoin(PropertiesLabelRelation::tableName() . ' b', 'a.properties_id=b.properties_id')
            ->leftJoin(PropertiesLabel::tableName() . ' c', 'c.properties_label_id=b.properties_label_id')
            ->leftJoin(Region::tableName() . ' d', 'd.region_code=a.region_code')
            ->leftJoin(HouseType::tableName() . ' e', 'e.properties_id=a.properties_id')
            ->where(['f.user_id' => $this->_userId])
            ->groupBy('a.properties_id')
            ->orderBy('f.user_distribution_id')
            ->offset($offset)
            ->limit(\Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['square_metre_min'] = floatval($v['square_metre_min']);
            $v['square_metre_max'] = floatval($v['square_metre_max']);
            $v['commission'] = floatval($v['commission']);
            $v['register_time'] = date('Y.m.d', $v['register_time']);
            $v['sale_time'] = date('Y.m.d', $v['sale_time']);
            $v['pic'] = json_decode($v['pic']);
            $v['sale_status'] = Yii::$app->params['sale_status'][$v['sale_status']];
            $model[$k] = $v;
        }

        return response($model);
    }

    /**
     * 我的楼盘分销
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionProperties()
    {
        $page          = Yii::$app->request->post('page', 1); // 页码
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $sql = "SELECT
                b.properties_id,
                b.name,
                b.pic,
                c.region_name,
                ( SELECT COUNT( 1 ) FROM ".InviteReg::tableName()." WHERE source = 2 AND b.properties_id = source_id ) count
            FROM
                ".PropertiesAdviserRelation::tableName()." a
                LEFT JOIN ".Properties::tableName()." b ON a.properties_id = b.properties_id
                LEFT JOIN ".Region::tableName()." c ON b.region_code = c.region_code
            WHERE
                a.user_id = {$this->_userId}
            ORDER BY a.properties_adviser_relation_id DESC     
            LIMIT {$offset}, ". Yii::$app->params['pageSize'];

        $model = Yii::$app->getDb()->createCommand($sql)->queryAll();
        foreach ($model as $k => $v)
        {
            $v['pic'] = json_decode($v['pic']);
            $model[$k] = $v;
        }

        return response($model);
    }

    /**
     * 我的分销
     * @return array
     */
    public function actionDistributionCount()
    {
        $response = [];
        $response['properties_count'] = PropertiesAdviserRelation::find()->where(['user_id' => $this->_userId])->count(1);
        $response['distribution_count'] = UserDistribution::find()->where(['user_id' => $this->_userId])->count(1);
        $response['invite_count'] = InviteReg::find()->where([
            'invite_user_id' => $this->_userId
        ])->count(1);

        return response($response);
    }

    /**
     * 退出登录
     * @return array
     */
    public function actionLogout()
    {
        $accessToken = substr(Yii::$app->request->headers['authorization'], 7);
        $key = 'login:' . $accessToken;
        Yii::$app->redis->del($key);
        return response();
    }

}
