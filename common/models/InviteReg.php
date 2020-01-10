<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_invite_reg".
 *
 * @property int $invite_reg_id 主键ID
 * @property int $reg_user_id 被邀请者ID
 * @property int $invite_user_id 发起邀请者ID
 */
class InviteReg extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%invite_reg}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reg_user_id', 'invite_user_id'], 'integer'],
            [['invite_user_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'invite_reg_id' => 'Invite Reg ID',
            'reg_user_id' => 'Reg User ID',
            'invite_user_id' => 'Invite User ID',
        ];
    }
}
