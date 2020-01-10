<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_house_type".
 *
 * @property int $house_type_id 主键ID
 * @property int $properties_id 楼盘表ID
 * @property string $name 户型名
 * @property string $pic 图片
 * @property string $square_metre平米
 * @property int $room_category_id 1一室2二室3三室4四室5五室
 * @property string $price 总价(单位:万)
 * @property int $status 0售罄1在售2待售
 * @property string $direction 朝向
 * @property string $distribute 户型分布
 */
class HouseType extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%house_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['properties_id', 'room_category_id', 'status'], 'integer'],
            [['square_metre'], 'required'],
            [['square_metre','price'], 'number'],
            [['name'], 'string', 'max' => 45],
            [['pic'], 'string', 'max' => 1800],
            [['direction'], 'string', 'max' => 30],
            [['distribute'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'house_type_id' => 'House Type ID',
            'properties_id' => 'Properties ID',
            'name' => 'Name',
            'pic' => 'Pic',
            'square_metre' => 'Square Metre Square Metre Square Metre',
            'room_category_id' => 'Room Category ID',
            'price' => 'Price',
            'status' => 'Status',
            'direction' => 'Direction',
            'distribute' => 'Distribute',
        ];
    }
}
