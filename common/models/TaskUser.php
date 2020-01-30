<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_task_user".
 *
 * @property int $task_user_id 主键
 * @property int $user_id 用户ID
 * @property int $task_id 任务ID
 * @property int $status 状态:0未开始1进行中2未完成3已完成4已领取奖励
 * @property int $create_time 接任务时间
 */
class TaskUser extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%task_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'task_id', 'status', 'create_time'], 'integer'],
            [['user_id', 'task_id'], 'required'],
            ['create_time', 'default', 'value' => time()]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'task_user_id' => '主键ID',
            'user_id' => '用户ID',
            'task_id' => '任务ID',
            'status' => '状态',
            'create_time' => '接任务时间',
        ];
    }
}
