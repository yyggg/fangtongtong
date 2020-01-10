<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_join_group_buying".
 *
 * @property int $join_group_buying_id 主键ID
 * @property int $user_id 加入拼团的用户ID
 * @property int $properties_id 楼盘ID
 * @property int $group_buying_id 拼团活动ID
 * @property int $is_cancel 状态:0参与拼团中1已取消拼团
 * @property int $join_time 加入时间
 */
class JoinGroupBuying extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ftt_join_group_buying';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'properties_id', 'group_buying_id', 'is_cancel', 'join_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'join_group_buying_id' => 'Join Group Buying ID',
            'user_id' => 'User ID',
            'properties_id' => 'Properties ID',
            'group_buying_id' => 'Group Buying ID',
            'is_cancel' => 'Is Cancel',
            'join_time' => 'Join Time',
        ];
    }
}
