<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_properties_answers".
 *
 * @property int $properties_answers_id 主键ID
 * @property int $properties_ask_id 提问表ID
 * @property int $answers_user_id 回答者用户ID
 * @property string $content 内容
 * @property int $create_time 回答时间
 */
class PropertiesAnswers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ftt_properties_answers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['properties_ask_id', 'answers_user_id', 'create_time'], 'integer'],
            [['content'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'properties_answers_id' => 'Properties Answers ID',
            'properties_ask_id' => 'Properties Ask ID',
            'answers_user_id' => 'Answers User ID',
            'content' => 'Content',
            'create_time' => 'Create Time',
        ];
    }
}
