<?php

namespace common\models;

use Yii;

/**
 * 楼盘收藏
 *
 * This is the model class for table "ftt_properties_collect".
 *
 * @property int $collect_id 主键ID
 * @property int $properties_id 楼盘ID
 * @property int $user_id 用户ID
 * @property int $create_time 创建时间
 */
class PropertiesCollect extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%properties_collect}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'properties_id', 'create_time'], 'integer'],
            ['create_time', 'default', 'value' => time()]
        ];
    }
}
