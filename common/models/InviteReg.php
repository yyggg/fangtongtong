<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_invite_reg".
 *
 * @property int $invite_reg_id 主键ID
 * @property int $reg_user_id 被邀请者ID
 * @property int $invite_user_id 发起邀请者ID
 * @property int $level 层级关系
 * @property int $source 来源: 1邀请注册任务
 * @property int $source_id 来源 ID
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
            [['reg_user_id', 'invite_user_id', 'level', 'source', 'source_id'], 'integer'],
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
            'level' => 'Level',
            'source' => 'Source',
            'source_id' => 'Source ID',
        ];
    }


    /**
     * 生成层级关系
     * @param int $user_id 邀请者用户ID
     * @param int $reg_user_id 被邀请者用户ID
     * @param int $source 来源
     * @param int $source_id 来源ID
     */
    public static function createLevelRelation($user_id, $reg_user_id, $source = 0, $source_id = 0)
    {
        $lowerRelationModel = new InviteReg();
        $allUser = InviteReg::find()->where(['reg_user_id' => $user_id])->limit(3);

        $lowerRelationModel->source = $source;
        $lowerRelationModel->source_id = $source_id;
        $lowerRelationModel->invite_user_id = $user_id;
        $lowerRelationModel->reg_user_id = $reg_user_id;
        $lowerRelationModel->level = 1;
        $lowerRelationModel->save(false);

        if($allUser){
            foreach ($allUser as $v)
            {
                $lowerRelationModel->invite_user_id = $v['user'];
                $lowerRelationModel->level += 1;
                $lowerRelationModel->reg_user_id = $reg_user_id;
                $lowerRelationModel->save(false);
            }
        }
    }
}
