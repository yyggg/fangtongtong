<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_user_distribution".
 *
 * @property int $user_distribution_id 主键ID
 * @property int $properties_id 楼盘 ID
 * @property int $user_id 用户 ID
 * @property number $commission 佣金
 * @property int $register_time 登记时间
 * @property int $sale_time 销售时间
 */
class UserDistribution extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_distribution}}';
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_distribution_id' => 'User Distribution ID',
            'properties_id' => 'Properties Id',
            'user_id' => 'User Id',
            'commission' => 'Commission',
            'register_time' => 'Register Time',
            'sale_time' => 'Sale Time',
        ];
    }
}
