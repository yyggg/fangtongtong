<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_user_history_city".
 *
 * @property int $history_city_id 主键ID
 * @property int $user_id 用户ID
 * @property string $region_code 区域码
 * @property string $name 城市名称
 */
class UserHistoryCity extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_history_city}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['region_code'], 'string', 'max' => 8],
            [['name'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'history_city_id' => 'History City ID',
            'user_id' => 'User ID',
            'region_code' => 'Region Code',
            'name' => 'Name',
        ];
    }
}
