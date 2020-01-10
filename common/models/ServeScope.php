<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ftt_serve_scope".
 *
 * @property int $serve_scope_id 主键ID
 * @property string $serve_name 服务名称
 */
class ServeScope extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ftt_serve_scope';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['serve_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'serve_scope_id' => 'Serve Scope ID',
            'serve_name' => 'Serve Name',
        ];
    }
}
