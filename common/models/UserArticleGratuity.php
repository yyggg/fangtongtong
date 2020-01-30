<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_user_article_gratuity".
 *
 * @property int $user_article_gratuity_id 主键
 * @property int $user_id 打赏用户ID
 * @property int $art_user_id 被打赏用户ID
 * @property string $money 打赏金额
 * @property int $status 状态：0未支付1取消支付2已支付
 * @property int $create_time 创建时间
 */
class UserArticleGratuity extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_article_gratuity}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'art_user_id', 'status', 'create_time'], 'integer'],
            [['money'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_article_gratuity_id' => 'User Article Gratuity ID',
            'user_id' => 'User ID',
            'art_user_id' => 'Art User ID',
            'money' => 'Money',
            'status' => 'Status',
            'create_time' => 'Create Time',
        ];
    }
}
