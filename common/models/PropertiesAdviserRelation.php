<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_properties_adviser_relation".
 *
 * @property int $properties_adviser_relation_id 主键ID
 * @property int $properties_id 楼盘表ID
 * @property int $user_id 顾问用户ID
 */
class PropertiesAdviserRelation extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%properties_adviser_relation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['properties_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'properties_adviser_relation_id' => 'Properties Adviser Relation ID',
            'properties_id' => 'Properties ID',
            'user_id' => 'User ID',
        ];
    }
}
