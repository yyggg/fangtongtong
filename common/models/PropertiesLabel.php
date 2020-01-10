<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_properties_label".
 *
 * @property int $properties_label_id 主键ID
 * @property string $label_name 标签名称
 */
class PropertiesLabel extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%properties_label}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['label_name'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'properties_label_id' => 'Properties Label ID',
            'label_name' => 'Label Name',
        ];
    }
}
