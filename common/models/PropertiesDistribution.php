<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_comment".
 *
 * @property int $ad_id 主键ID
 * @property string $title 标题
 * @property string $link 链接
 * @property string $pic 图片地址 json
 * @property int $type 类型：0首页
 * @property int $status 状态：0不显示 1显示
 * @property int $create_time 创建时间
 */
class PropertiesDistribution extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%properties_distribution}}';
    }
}
