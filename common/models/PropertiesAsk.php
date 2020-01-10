<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_properties_ask".
 *
 * @property int $properties_ask_id 主键ID
 * @property int $properties_id 楼盘ID
 * @property int $ask_user_id 提问人用户ID
 * @property string $title 提问标题
 * @property int $create_time 提问时间
 */
class PropertiesAsk extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%properties_ask}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['properties_id', 'ask_user_id', 'create_time'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'properties_ask_id' => 'Properties Ask ID',
            'properties_id' => 'Properties ID',
            'ask_user_id' => 'Ask User ID',
            'title' => 'Title',
            'create_time' => 'Create Time',
        ];
    }
}
