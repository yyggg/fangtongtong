<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_withdraw".
 *
 * @property int $withdraw_id 主键
 * @property int $user_id 用户ID
 * @property string $amount 金额
 * @property int $status 0申请中1成功2失败
 * @property int $create_time 创建时间
 * @property int $update_time 最后更新时间
 */
class Withdraw extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%withdraw}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['amount'], 'required'],
            [['amount'], 'number'],
            ['create_time', 'default', 'value' => time()]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'withdraw_id' => 'Withdraw ID',
            'user_id' => 'User ID',
            'amount' => 'Amount',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
