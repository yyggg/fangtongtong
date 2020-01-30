<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_subscribe_properties".
 *
 * @property int $subscribe_properties_id 主键ID
 * @property int $properties_id 楼盘ID
 * @property int $user_id 用户ID
 * @property int $status 0取消1关注
 * @property int $create_time 关注时间
 */
class SubscribeProperties extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%subscribe_properties}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['properties_id', 'user_id', 'status', 'create_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'subscribe_properties_id' => 'Subscribe Properties ID',
            'properties_id' => 'Properties ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'create_time' => 'Create Time',
        ];
    }
}
