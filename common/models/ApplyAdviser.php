<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_apply_adviser".
 *
 * @property int $apply_adviser_id 主键ID
 * @property int $user_id 用户ID
 * @property string $name 姓名
 * @property string $idcard 身份证号码
 * @property string $picture 相片
 * @property int $status 0申请中1已通过2失败
 * @property int $create_time 申请时间
 */
class ApplyAdviser extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%apply_adviser}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 4],
            [['idcard'], 'string', 'max' => 18],
            [['picture'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'apply_adviser_id' => 'Apply Adviser ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'idcard' => 'Idcard',
            'picture' => 'Picture',
            'status' => 'Status',
            'create_time' => 'Create Time',
        ];
    }
}
