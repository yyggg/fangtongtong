<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_properties_label_relation".
 *
 * @property int $properties_label_relation_id 主键ID
 * @property int $properties_label_id 楼盘标签ID
 * @property int $properties_id 楼盘ID
 */
class PropertiesLabelRelation extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return "{{%properties_label_relation}}";
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['properties_label_id', 'properties_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'properties_label_relation_id' => 'Properties Label Relation ID',
            'properties_label_id' => 'Properties Label ID',
            'properties_id' => 'Properties ID',
        ];
    }
}
