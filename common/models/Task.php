<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_task".
 *
 * @property int $task_id 主键ID
 * @property int $properties_id 楼盘ID
 * @property int $type 任务类型:0邀请注册1邀请参加活动2分享活动3分享顾问点评
 * @property int $task_reward_id 任务奖励ID
 * @property string $title 标题
 * @property string $pic 图片
 * @property string $rule 规则
 * @property int $s_time 报名开始时间
 * @property int $e_time 报名结束时间
 * @property int $condition 完成条件，如邀请1个人，分享一次
 * @property int $create_time 创建时间
 */
class Task extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%task}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['properties_id', 'type', 'task_reward_id', 's_time', 'e_time', 'condition', 'create_time'], 'integer'],
            [['rule'], 'string'],
            [['title'], 'string', 'max' => 120],
            [['pic'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'task_id' => 'Task ID',
            'properties_id' => 'Properties ID',
            'type' => 'Type',
            'task_reward_id' => 'Task Reward ID',
            'title' => 'Title',
            'pic' => 'Pic',
            'rule' => 'Rule',
            's_time' => 'S Time',
            'e_time' => 'E Time',
            'condition' => 'Condition',
            'create_time' => 'Create Time',
        ];
    }
}
