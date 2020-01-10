<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_properties_information".
 *
 * @property int $properties_information_id
 * @property int $properties_id
 * @property string $title
 * @property string $pic
 * @property string $content
 * @property int $create_time
 */
class PropertiesInformation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ftt_properties_information';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['properties_id', 'create_time'], 'integer'],
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
            'properties_information_id' => 'Properties Information ID',
            'properties_id' => 'Properties ID',
            'title' => 'Title',
            'pic' => 'Pic',
            'content' => 'Content',
            'create_time' => 'Create Time',
        ];
    }
}
