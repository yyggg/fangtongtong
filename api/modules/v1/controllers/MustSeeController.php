<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-08 15:46
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseCotroller;
use common\models\Comment;
use common\models\GroupBuying;
use common\models\Properties;
use common\models\Task;
use common\models\User;
use Yii;

class MustSeeController extends BaseCotroller
{
    /**
     * 必看首页
     * @return array
     */
    public function actionIndex()
    {
        // 看拼团

        $activity = GroupBuying::find()
            ->select([
                'group_buying_id','title', 'people', 'pic'
            ])
            ->orderBy('group_buying_id DESC')
            ->limit(2)
            ->asArray()
            ->all();

        // 任务
        $task = Task::find()
            ->select(['task_id', 'title', 'type'])
            ->asArray()
            ->all();

        if ($task)
        {
            foreach ($task as $k => $v)
            {
                $v['type_title'] = Yii::$app->params['task_type'][$v['type']];
                $task[$k] = $v;
            }
        }

        // 点评
        $comment = Comment::find()
            ->alias('a')
            ->select([
                'a.comment_id','a.user_id','a.content','a.create_time','b.headimgurl','b.nickname', 'b.is_adviser',
                'c.pic', 'c.name', 'c.properties_id', 'c.sale_status'
            ])
            ->leftJoin(User::tableName() . ' b', 'a.user_id=b.user_id')
            ->leftJoin(Properties::tableName() . ' c', 'a.properties_id=c.properties_id')
            ->orderBy('comment_id desc')
            ->asArray()
            ->one();

        if ($comment)
        {
            $comment['create_time'] = date('H:i', $comment['create_time']);
            $comment['sale_status'] = Yii::$app->params['sale_status'][$comment['sale_status']];
        }

        $data = [
            'activity' => $activity,
            'task' => $task,
            'comment' => $comment,
        ];

        return response($data);
    }
}
