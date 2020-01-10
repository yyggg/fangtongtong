<?php
namespace api\models;

use common\models\Base;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\IdentityInterface;

class User extends Base implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public static function tableName()
    {
        return '{{%user}}';
    }


    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }




    public static function findIdentityByAccessToken($token, $type = null)
    {
        $userId = (int)Yii::$app->redis->get($token);

        return static::findOne(['user_id' => $userId, 'status' => self::STATUS_ACTIVE]);
    }


    public static function findByPassword($phone = '', $password = '')
    {
        $user = static::findOne(['phone' => $phone, 'status' => self::STATUS_ACTIVE])->toArray();

        if (!$user)
        {
            return false;
        }

        return Yii::$app->security->validatePassword($password, $user['password']) ? $user : false;
    }


    public static function generateAccessToken($userId = '')
    {
        $accessToken = Yii::$app->security->generateRandomString();

        $key = 'login:' . $accessToken;
        Yii::$app->redis->set($key, $userId);
        Yii::$app->redis->expire($key, 60);

        return $accessToken;
    }





    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

}
