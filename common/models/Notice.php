<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_notice".
 *
 * @property int $notice_id 主键ID
 * @property int $category_id 0系统通知1分销消息通知2奖励通知
 * @property int $sub_category_id 此ID对应category_id字段,用于显示通知类型标题
 * @property int $user_id 通知的用户ID,0代表系统所有用户
 * @property string $title 一句话简述标题
 * @property int $relation_id 关系ID,可以是楼盘ID,任务ID等
 * @property int $notice_time 通知时间
 * @property int $create_time 创建时间
 */
class Notice extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%notice}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'sub_category_id', 'user_id', 'relation_id', 'notice_time', 'create_time'], 'integer'],
            [['title'], 'string', 'max' => 120],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'notice_id' => 'Notice ID',
            'category_id' => 'Category ID',
            'sub_category_id' => 'Sub Category ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'relation_id' => 'Relation ID',
            'notice_time' => 'Notice Time',
            'create_time' => 'Create Time',
        ];
    }
}
