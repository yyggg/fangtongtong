<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_region".
 *
 * @property int $region_id
 * @property string $name
 * @property string $region_code
 * @property string $parent_code
 * @property int $level
 */
class Region extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%region}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level'], 'integer'],
            [['name'], 'string', 'max' => 18],
            [['region_code', 'parent_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'region_id' => 'Region ID',
            'name' => 'Name',
            'region_code' => 'Region Code',
            'parent_code' => 'Parent Code',
            'level' => 'Level',
        ];
    }
}
