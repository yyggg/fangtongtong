<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_encyclopedia_category".
 *
 * @property int $encyclopedia_category_id 主键ID
 * @property string $name 类名
 */
class EncyclopediaCategory extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%encyclopedia_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'encyclopedia_category_id' => 'Encyclopedia Category ID',
            'name' => 'Name',
        ];
    }
}
