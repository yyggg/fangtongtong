<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use yii\filters\RateLimitInterface;

/**
 * This is the model class for table "t_user".
 *
 * @property string $id
 * @property string $username
 * @property string $nickname
 * @property string $head_pic
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $access_token
 * @property string $mobile
 * @property string $email
 * @property integer $status
 * @property integer $r_id
 * @property string $update_time
 * @property string $created_address
 * @property string $created_ip
 * @property integer $last_login_date
 * @property string $last_login_ip
 * @property string $last_login_address
 * @property integer $integral
 * @property string $balance
 * @property string $create_time
 */

class User extends ActiveRecord implements IdentityInterface,RateLimitInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * 获取上级ID
     * @return \yii\db\ActiveQuery
     */
    public function getHigherLevel(){
        return $this->hasOne(LowerRelation::className(),['lower_user_id' => 'id'])->where(['level' => 1]);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
			[['status'], 'integer'],
            [['username'], 'required'],
            ['username', 'string', 'min'=>6, 'max' => 50],
            ['bank_number', 'string', 'min'=>16, 'max' => 50],
			[['real_name','bank_type'], 'string', 'max' => 32],
            [['alipay','tenpay','bank_remark_phone','trans_password','password_hash','verified','update_time','status '],'safe'],
            [['username'],'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
		$user = static::find()->where(['access_token'=>$token , 'status' => self::STATUS_ACTIVE])->one();
		if($user){
			return $user;
		}else{
			return null;
		}
    }

	
    // 返回某一时间允许请求的最大数量，比如设置10秒内最多5次请求（小数量方便我们模拟测试）
    public  function getRateLimit($request, $action){  
         return [5, 10];  
    }
     
    // 回剩余的允许的请求和相应的UNIX时间戳数 当最后一次速率限制检查时
    public  function loadAllowance($request, $action){  
         return [$this->allowance, $this->allowance_updated_at];  
    }  
     
    // 保存允许剩余的请求数和当前的UNIX时间戳
    public  function saveAllowance($request, $action, $allowance, $timestamp){ 
        $this->allowance = $allowance;  
        $this->allowance_updated_at = $timestamp;  
        $this->save();  
    }  
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
	public static function findByEmail($email){
		return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
	}
    /**
     * Finds user by mobile
     *
     * @param string $mobile
     * @return static|null
     */
	public static function findByMobile($mobile){
		return static::findOne(['mobile' => $mobile, 'status' => self::STATUS_ACTIVE]);
	}
	/**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken($expire_time)
    {
        $this->access_token = Yii::$app->security->generateRandomString().'_'.$expire_time;
        return $this->access_token;
    }
    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户账号',
            'real_name' => '真实姓名',
            'amount' => '本金',
            'profit' => '总收益',
            'alipay' => '支付宝',
            'bank_branch' => '开户支行',
            'bank_remark_phone' => '预留手机号',
            'tenpay' => '微信',
            'bank_type' => '银行类型',
            'bank_branch ' => '开户支行',
            'bank_number' => '银行卡卡号',
            'verified' => '信息认证',
            'auth_key' => 'AuthKey',
            'password_hash' => '登录密码',
            'trans_password' => '交易密码',
            'password_reset_token' => '密码重置码',
            'access_token' => 'Access Token',
            'status' => '状态',
            'create_time' => '注册时间',
            'update_time' => '修改时间',
        ];
    }
	
	public static function getUserName($id){
		return self::findOne($id)->username;
	}
	/**
	 * 获取类别的下拉菜单
	 * @return type
	 */
	public static function dropDown($r_id){
		$data = self::find()->where(['>','r_id',$r_id])->asArray()->all();
		$data_list = ArrayHelper::map($data, 'id', 'username');
		return $data_list;
	} 
}
