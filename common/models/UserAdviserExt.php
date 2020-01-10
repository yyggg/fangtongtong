<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_user_adviser_ext".
 *
 * @property int $adviser_id 主键ID
 * @property int $user_id 用户ID
 * @property string $region_code 区域码
 * @property string $serve_length 服务年限
 * @property int $serve_people 服务人数
 * @property string $overall_score 综合评分
 */
class UserAdviserExt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ftt_user_adviser_ext';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'serve_people'], 'integer'],
            [['serve_length', 'overall_score'], 'number'],
            [['region_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'adviser_id' => 'Adviser ID',
            'user_id' => 'User ID',
            'region_code' => 'Region Code',
            'serve_length' => 'Serve Length',
            'serve_people' => 'Serve People',
            'overall_score' => 'Overall Score',
        ];
    }
}
