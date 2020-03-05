<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-08 15:46
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseController;
use api\models\User;
use common\models\Comment;
use common\models\GroupBuying;
use common\models\GroupBuyingTpl;
use common\models\Properties;
use common\models\Task;
use Yii;

class MustSeeController extends BaseController
{
    /**
     * 必看首页
     * @return array
     */
    public function actionIndex()
    {
        // 看拼团

        $activity = GroupBuying::find()
            ->alias('a')
            ->select([
                'a.group_buying_id','b.title', 'b.people', 'b.remark', 'c.pic'
            ])
            ->leftJoin(GroupBuyingTpl::tableName() . ' b', 'a.group_buying_tpl_id = b.group_buying_tpl_id')
            ->leftJoin(Properties::tableName() . ' c', 'a.properties_id = c.properties_id')
            ->where(['a.status' => '0'])
            ->andWhere(['>', 'b.e_time', time()])
            ->orderBy('a.group_buying_id DESC')
            ->limit(2)
            ->asArray()
            ->all();

        foreach ($activity as $k => $v)
        {
            if ($v['pic'])
            {
                $v['pic'] = json_decode($v['pic']);
            }
            $activity[$k] = $v;
        }

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
            if ($comment['pic']){
                $comment['pic'] = json_decode($comment['pic']);
            }
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

    /**
     * 获取分享信息
     * @return array
     */
    public function actionShareInfo()
    {
        $propertiesId = Yii::$app->request->get('properties_id', 0);
        $userId = Yii::$app->request->get('user_id', 0);

        $model = Properties::find()
            ->select(['name', 'pic'])
            ->where(['properties_id' => $propertiesId])
            ->asArray()
            ->one();
        $model['pic'] = json_encode($model['pic']);
        $model['invite_code'] = User::createInviteCode($userId);

        return response($model);
    }
}
