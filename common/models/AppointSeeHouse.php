<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_appoint_see_house".
 *
 * @property int $appoint_see_room_id 主键ID
 * @property int $user_id 用户ID
 * @property int $house_type_id 户型ID
 * @property int $properties_id 楼盘ID
 * @property int $adviser_id 负责接单顾问ID
 * @property int $date 日期
 * @property string $time_slot 时间段
 * @property int $status 0预约已提交1顾问已接单2预约已关闭
 * @property int $create_time 创建时间
 */
class AppointSeeHouse extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%appoint_see_house}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'house_type_id', 'properties_id', 'adviser_id', 'date','create_time', 'status'], 'integer'],
            [['idcard','name', 'phone','remark'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'appoint_see_room_id' => 'Appoint See Room ID',
            'user_id' => 'User ID',
            'house_type_id' => 'House Type ID',
            'properties_id' => 'Properties ID',
            'adviser_id' => 'Adviser ID',
            'date' => 'Date',
            'idcard' => '身份证后6位',
            'status' => 'Status',
            'create_time' => 'Create Time',
        ];
    }
}
