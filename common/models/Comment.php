<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_comment".
 *
 * @property int $acomment_id 主键ID
 * @property int $properties_id 楼盘ID
 * @property string $content 内容
 * @property int $user_id 评论者用户ID
 * @property int $author_user_id 被点评者用户ID
 * @property int $create_time 点评时间
 */
class Comment extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['properties_id', 'user_id', 'author_user_id', 'create_time'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => 'Comment ID',
            'properties_id' => 'Properties ID',
            'content' => 'Content',
            'user_id' => 'User ID',
            'author_user_id' => 'Author User ID',
            'create_time' => 'Create Time',
        ];
    }
}
