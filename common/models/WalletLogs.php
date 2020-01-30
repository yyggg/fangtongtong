<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_wallet_logs".
 *
 * @property int $wallet_logs_id 主键
 * @property int $user_id 用户ID
 * @property int $type 类型：0积分1提现2佣金3打赏
 * @property int $type_id 类型ID：如订单ID
 * @property string $number 变动数值：如多少积分、返现多少、打赏多少
 * @property string $remark 备注：如积分收入
 * @property int $create_time 时间
 */
class WalletLogs extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wallet_logs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'type_id', 'create_time'], 'integer'],
            [['number'], 'number'],
            [['remark'], 'string', 'max' => 60],
            ['create_time', 'default', 'value' => time()]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'wallet_logs_id' => 'Wallet Logs ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'type_id' => 'Type ID',
            'number' => 'Number',
            'remark' => 'Remark',
            'create_time' => 'Create Time',
        ];
    }
}
