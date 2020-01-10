<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_properties".
 *
 * @property int $properties_id 主键ID
 * @property string $name 楼盘名称
 * @property int $price_metre 没每平米价格(单位:元)
 * @property int $price_avg 均价(单位:万)
 * @property string $price_total_min 最小总价(单位:万)
 * @property string $price_total_max 最大总价(单位:万)
 * @property string $random_id 随机ID
 * @property string $region_code 地区码
 * @property string $pic 图片
 * @property string $video 视频地址
 * @property int $open_time 开盘时间
 * @property int $high_remuneration 高佣金:0否1是
 * @property int $fast_get_remuneration 快速拿佣:0否1是
 * @property int $property_type_id 物业类型:0不限1住宅2别墅3商业4商铺5写字楼
 * @property int $down_payment_id 0零首付1=>10万首付,2=>20万首付,3=>30万首付,20=>其他
 * @property string $address 地址
 * @property int $recommend 是否推荐:0否1是
 * @property int $sale_status 0售罄1在售2待售
 * @property int $create_time 创建时间
 */
class Properties extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%properties}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price_metre', 'price_avg', 'open_time', 'high_remuneration', 'fast_get_remuneration', 'property_type_id', 'down_payment_id', 'recommend', 'sale_status', 'create_time'], 'integer'],
            [['price_total_min', 'price_total_max'], 'number'],
            [['name'], 'string', 'max' => 45],
            [['random_id'], 'string', 'max' => 22],
            [['region_code'], 'string', 'max' => 10],
            [['pic'], 'string', 'max' => 1800],
            [['video', 'address'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'properties_id' => 'Properties ID',
            'name' => 'Name',
            'price_metre' => 'Price Metre',
            'price_avg' => 'Price Avg',
            'price_total_min' => 'Price Total Min',
            'price_total_max' => 'Price Total Max',
            'random_id' => 'Random ID',
            'region_code' => 'Region Code',
            'pic' => 'Pic',
            'video' => 'Video',
            'open_time' => 'Open Time',
            'high_remuneration' => 'High Remuneration',
            'fast_get_remuneration' => 'Fast Get Remuneration',
            'property_type_id' => 'Property Type ID',
            'down_payment_id' => 'Down Payment ID',
            'address' => 'Address',
            'recommend' => 'Recommend',
            'sale_status' => 'Sale Status',
            'create_time' => 'Create Time',
        ];
    }
}
