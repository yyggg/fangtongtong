<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_encyclopedia".
 *
 * @property int $encyclopedia_id 主键ID
 * @property string $title 标题
 * @property string $pic 列表图片
 * @property int $encyclopedia_category_id 分类ID
 * @property string $content 内容
 * @property int $create_time 创建时间
 */
class Encyclopedia extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%encyclopedia}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['encyclopedia_category_id', 'create_time'], 'integer'],
            [['content'], 'string'],
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
            'encyclopedia_id' => 'Encyclopedia ID',
            'title' => 'Title',
            'pic' => 'Pic',
            'encyclopedia_category_id' => 'Encyclopedia Category ID',
            'content' => 'Content',
            'create_time' => 'Create Time',
        ];
    }
}
