<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_group_buying".
 *
 * @property int $group_buying_id 主键ID
 * @property int $group_buying_tpl_id 拼团模板ID
 * @property int $properties_id 楼盘ID
 * @property int $user_id 开团者用户ID
 * @property int $status 状态:0未开始1进行中2成功3失败
 * @property int $create_time 创建时间
 * @property int $success_time 成团时间
 */
class GroupBuying extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%group_buying}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['properties_id','group_buying_tpl_id'], 'required'],
            [['properties_id','group_buying_tpl_id', 'user_id', 'status', 'create_time','success_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'group_buying_id' => 'Group Buying ID',
            'group_buying_tpl_id' => 'Group Buying Tpl ID',
            'properties_id' => 'Properties ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'success_time' => 'Success Time',
        ];
    }
}
