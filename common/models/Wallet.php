<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_wallet".
 *
 * @property int $wallet_id 主键
 * @property int $user_id 用户ID
 * @property string $account 余额
 * @property string $points 积分
 */
class Wallet extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wallet}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['account', 'points'], 'required'],
            [['account', 'points'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'wallet_id' => 'Wallet ID',
            'user_id' => 'User ID',
            'account' => 'Account',
            'points' => 'Points',
        ];
    }
}
