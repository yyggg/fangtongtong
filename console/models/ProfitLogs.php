<?php
/**
 * Created 老杨
 * User: 260101081@qq.com
 * Date: 2018/10/9 17:12
 */

namespace console\models;

use yii\db\ActiveRecord;

class ProfitLogs extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%profit_logs}}';
    }
}