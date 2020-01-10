<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_serve_scope_relation".
 *
 * @property int $serve_scope_relation_id 主键ID
 * @property int $user_id 用户ID
 * @property int $serve_scope_id 用户服务范围ID
 */
class ServeScopeRelation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ftt_serve_scope_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'serve_scope_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'serve_scope_relation_id' => 'Serve Scope Relation ID',
            'user_id' => 'User ID',
            'serve_scope_id' => 'Serve Scope ID',
        ];
    }
}
