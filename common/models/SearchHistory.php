<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_search_history".
 *
 * @property int $search_id 主键
 * @property string $keyword 搜索词
 * @property int $user_id 用户id
 * @property int $type 搜索的类型:0楼盘1顾问
 */
class SearchHistory extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%search_history}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type'], 'integer'],
            [['keyword'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'search_id' => 'Search ID',
            'keyword' => 'Keyword',
            'user_id' => 'User ID',
            'type' => 'Type',
        ];
    }
}
