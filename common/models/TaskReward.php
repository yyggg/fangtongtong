<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_task_reward".
 *
 * @property int $task_reward_id 主键
 * @property int $number 数额
 * @property int $type 类型:0积分1京东券
 * @property string $name 名称
 * @property int $valid_stime 有效或使用开始时间
 * @property int $valid_etime 有效或使用结束时间
 */
class TaskReward extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%task_reward}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'type', 'valid_stime', 'valid_etime'], 'integer'],
            [['name'], 'string', 'max' => 35],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'task_reward_id' => 'Task Reward ID',
            'number' => 'Number',
            'type' => 'Type',
            'name' => 'Name',
            'valid_stime' => 'Valid Stime',
            'valid_etime' => 'Valid Etime',
        ];
    }
}
