<?php
namespace api\models;

use yii\base\Model;
use api\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $password = 'a123456'; //默认密码
    /**
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique'],
            ['password', 'safe',],
        ];
    }


    public function signup()
    {
        $errMsg = ['code' => 0, 'msg' => 'OK'];

        $user = new User();
        $user->username = $this->username;
        $user->setPassword($this->password);
        $user->generateAccessToken();

        if($user::findOne(['username' => $this->username]))
        {
            $errMsg['code'] = '1005';
            $errMsg['msg'] = '用户名已经存在';
            return $errMsg;
        }
        if($user->save())
        {
            $errMsg['id'] = $user->getId();
            return $errMsg;
        }
        $errMsg['code'] = '1004';
        $errMsg['msg'] = '注册失败';
        return  $errMsg['code'];
    }
}
