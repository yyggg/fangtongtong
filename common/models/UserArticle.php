<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_user_article".
 *
 * @property int $user_article_id 主键ID
 * @property int $user_id 用户ID
 * @property string $title 标题
 * @property string $content 内容
 * @property int $star 点赞数
 * @property int $comment 评论数
 * @property int $gratuity 打赏次数
 * @property int $create_time 创建时间
 */
class UserArticle extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_article}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'star', 'comment', 'gratuity', 'create_time'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 90],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_article_id' => 'User Article ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'content' => 'Content',
            'star' => 'Star',
            'comment' => 'Comment',
            'gratuity' => 'Gratuity',
            'create_time' => 'Create Time',
        ];
    }
}
