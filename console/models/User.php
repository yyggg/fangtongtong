<?php
/**
 * Created by 老杨.
 * User: 260101081@qq.com
 * Date: 2018/10/12 22:28
 */
namespace console\models;
class User extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return "{{%user}}";
    }

}