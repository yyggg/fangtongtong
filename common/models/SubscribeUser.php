<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_subscribe_user".
 *
 * @property int $subscribe_user_id 主键ID
 * @property int $user_id 用户ID
 * @property int $adviser_user_id 被关注的用户ID
 * @property int $status 0取消1关注
 * @property int $create_time 关注时间
 */
class SubscribeUser extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%subscribe_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'adviser_user_id', 'status', 'create_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'subscribe_user_id' => 'Subscribe User ID',
            'user_id' => 'User ID',
            'adviser_user_id' => 'Adviser User ID',
            'status' => 'Status',
            'create_time' => 'Create Time',
        ];
    }
}
