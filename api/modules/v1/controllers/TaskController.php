<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2019-12-31 19:40
 */
namespace api\modules\v1\controllers;

use api\models\User;
use common\models\InviteReg;
use common\models\Task;
use common\models\TaskReward;
use common\models\TaskUser;
use Yii;
use api\controllers\BaseController;


class TaskController extends BaseController
{

    /**
     * 任务详情
     * @return array
     */
    public function actionInfo()
    {
        $taskStatus = 0; // 任务状态 默认未开始
        $response = [];

        $taskId = Yii::$app->request->get('task_id', 0);
        $userId = Yii::$app->request->get('user_id', 0);

        // 任务详情
        $taskInfo = Task::find()
            ->alias('a')
            ->select([
                'a.pic', 'a.title','a.properties_id','a.rule','a.condition','a.s_time','a.e_time',
                'b.valid_stime', 'b.valid_etime','b.name','b.number'
            ])
            ->leftJoin(TaskReward::tableName() . ' b', 'a.task_reward_id = b.task_reward_id')
            ->where(['a.task_id' => $taskId])
            ->asArray()
            ->one();

        $taskInfo['s_time'] = date('Y.m.d H:i:s',  $taskInfo['s_time']);
        $taskInfo['e_time'] = date('Y.m.d H:i:s',  $taskInfo['e_time']);

        // 参加任务状态
        $joinTask = TaskUser::find()
            ->select(['status'])
            ->where(['user_id' => $userId, 'task_id' => $taskId])
            ->one();

        // 成功邀请用户
        $invite = InviteReg::find()
            ->alias('a')
            ->select([
                'b.nickname', 'b.headimgurl', 'b.create_time'
            ])
            ->leftJoin(User::tableName() . ' b', 'a.reg_user_id = b.user_id')
            ->where(['source' => '1', 'source_id' => $taskId])
            ->asArray()
            ->all();

        if ($joinTask)
        {
            $taskStatus = $joinTask->status;
            if ($taskStatus == 1)
            {
                // 如果任务时间结束前没达到邀请人数视为未完成
                if ((count($invite) < $taskInfo['condition']) && ($taskInfo['e_time'] < time()))
                {
                    $taskStatus = 2;
                    // 更新任务状态为未完成

                    $model = TaskUser::find()
                        ->where(['user_id' => $userId, 'task_id' => $taskId])
                        ->one();

                    $model->status = $taskStatus;
                    $model->save(false);
                }
            }
        }


        $response['task_info'] = $taskInfo;
        $response['task_status'] = $taskStatus;
        $response['task_status_label'] = Yii::$app->params['task_status'][$taskStatus];
        $response['invite_user'] = $invite;

        return response($response);

    }

    /**
     * 创建任务
     * @return array
     */
    public function actionCreate()
    {
        $params = Yii::$app->request->post();
        $model = new TaskUser();

        if ($model->load(['TaskUser' => $params]) && $model->save())
        {
            return response();
        }
        return response([], '30030', $model->getErrorSummary(false)[0]);
    }

    /**
     * 我的任务
     * @return array
     */
    public function actionIndexByUser()
    {
        $status = Yii::$app->request->get('status', 1);
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = TaskUser::find()
            ->select([
                'a.task_id',
                'b.title',
                'b.pic',
                'c.number',
                'c.valid_stime',
                'c.valid_etime'
            ])
            ->alias('a')
            ->leftJoin(Task::tableName() . ' b', 'a.task_id = b.task_id')
            ->leftJoin(TaskReward::tableName() . ' c', 'c.task_reward_id = c.task_reward_id');

        $model->where(['a.user_id' => $this->_userId]);

        if ($status == 1)
        {
            $model->andWhere(['>', 'b.e_time', time()]);
            $model->andWhere(['a.status' => $status]);
        }
        elseif($status == 2)
        {
            $model->andWhere(['<', 'b.e_time', time()]);
            $model->andWhere(['<', 'a.status', 3]);
        }
        else
        {
            $model->andWhere(['a.status' => $status]);
        }
//echo $model->createCommand()->getRawSql();die;
        $task = $model->orderBy('a.task_user_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($task as $k => $v)
        {
            $v['valid_stime'] = date('Y.m.d', $v['valid_stime']);
            $v['valid_etime'] = date('Y.m.d', $v['valid_etime']);
            $task[$k] = $v;
        }

        return response($task);
    }

}
