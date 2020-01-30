<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-27 11:53
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseController;
use common\models\RewardLogs;
use common\models\Task;
use common\models\TaskReward;
use Yii;

class RewardController extends BaseController
{
    /**
     * 历史奖励
     * @return array
     */
    public function actionLogs()
    {
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = RewardLogs::find()
            ->alias('a')
            ->select([
                'a.create_time',
                'b.title',
                'c.number'
            ])
            ->leftJoin(Task::tableName() . ' b', 'a.task_id = b.task_id')
            ->leftJoin(TaskReward::tableName() . ' c', 'b.task_reward_id = c.task_reward_id')
            ->where(['a.user_id' => $this->_userId])
            ->orderBy('a.user_task_reward_logs_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['create_time'] = date('Y.m.d H:i', $v['create_time']);
            $model[$k] = $v;
        }

        return response($model);
    }
}
