<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_hot_city".
 *
 * @property int $hot_city_id 主键ID
 * @property string $region_code 区域码
 * @property string $name 城市名称
 */
class HotCity extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%hot_city}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_code'], 'string', 'max' => 6],
            [['name'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hot_city_id' => 'Hot City ID',
            'region_code' => 'Region Code',
            'name' => 'Name',
        ];
    }
}
