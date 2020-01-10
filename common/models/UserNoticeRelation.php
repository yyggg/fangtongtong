<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_user_notice_relation".
 *
 * @property int $user_notice_relation_id 主键
 * @property int $user_id 用户id
 * @property int $notice_id 通知id
 * @property int $status 状态:0未读1已读
 */
class UserNoticeRelation extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_notice_relation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'notice_id', 'status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_notice_relation_id' => 'User Notice Relation ID',
            'user_id' => 'User ID',
            'notice_id' => 'Notice ID',
            'status' => 'Status',
        ];
    }
}
