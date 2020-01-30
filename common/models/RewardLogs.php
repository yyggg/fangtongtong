<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_user_task_reward_logs".
 *
 * @property int $user_task_reward_logs_id 主键
 * @property int $user_id 用户 ID
 * @property int $reward_id 奖励 ID
 * @property int $task_id 任务 ID
 * @property int $create_time 领奖时间
 */
class RewardLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ftt_user_task_reward_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'reward_id', 'task_id', 'create_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_task_reward_logs_id' => 'User Task Reward Logs ID',
            'user_id' => 'User ID',
            'reward_id' => 'Reward ID',
            'task_id' => 'Task ID',
            'create_time' => 'Create Time',
        ];
    }
}
