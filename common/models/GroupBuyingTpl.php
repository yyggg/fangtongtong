<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_group_buying_tpl".
 *
 * @property int $group_buying_tpl_id 主键ID
 * @property string $title 标题
 * @property string $remark 备注
 * @property string $pic 图片
 * @property string $rule 规则
 * @property int $s_time 报名开始时间
 * @property int $e_time 报名结束时间
 * @property int $people 成团人数
 * @property int $create_time 创建时间
 */
class GroupBuyingTpl extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%group_buying_tpl}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rule'], 'string'],
            [['s_time', 'e_time', 'people', 'create_time'], 'integer'],
            [['title'], 'string', 'max' => 120],
            [['remark'], 'string', 'max' => 30],
            [['pic'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'group_buying_tpl_id' => 'Group Buying Tpls ID',
            'title' => 'Title',
            'remark' => 'Remark',
            'pic' => 'Pic',
            'rule' => 'Rule',
            's_time' => 'S Time',
            'e_time' => 'E Time',
            'people' => 'People',
            'create_time' => 'Create Time',
        ];
    }
}
