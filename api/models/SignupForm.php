<?php
namespace api\models;

use api\models\User;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $phone;
    public $password;
    public $sms_code;

    /**
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ['phone', 'trim'],
            [['sms_code', 'password', 'phone'], 'required'],
            [['phone'],'match','pattern'=>'/^[1][3578][0-9]{9}$/'],
            ['phone', 'unique', 'targetClass' => '\api\models\User', 'message' => '手机号已被注册'],
            ['password', 'string', 'max' => 18, 'min' => 6],
            //['agree', 'required', 'requiredValue'=>true,'message'=>'请确认是否同意隐私权协议条款'],
        ];
    }


    public function signup()
    {
        if (!$this->validate())
        {
            return false;
        }

        $user = new User();
        $user->phone = $this->phone;
        $user->password = $user->setPassword($this->password);
        if (!$user->save())
        {
            return false;
        }

        return true;
    }

    public function attributeLabels()
    {
        return [
            'phone'    => '手机号',
            'sms_code' => '手机验证码',
            'password' => '密码',
        ];
    }
}
