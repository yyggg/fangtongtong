<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;

use common\models\Notice;
use common\models\UserNoticeRelation;
use Yii;
use api\controllers\BaseController;

class NoticeController extends BaseController
{
    /**
     * 系统公告
     * @return array
     */
    public function actionSys()
    {
        $model = Notice::find()
            ->select(['title','notice_id','create_time','content'])
            ->where(['category_id' => 0, 'sub_category_id' => 0])
            ->orderBy('notice_id desc')->asArray()->all();
        return response($model);
    }
    /**
     * 消息通知主页
     * @return array
     */
    public function actionIndex()
    {
        $data = [];
        $stime  = strtotime(date('Y-m-d'));

        // 系统类型通知
        $sysNotice = UserNoticeRelation::find()
            ->alias('a')
            ->select(['a.notice_id', 'b.category_id', 'b.title', 'b.notice_time'])
            ->leftJoin(Notice::tableName(). ' b', 'a.notice_id = b.notice_id')
            //->where(['or', ['like', 'user_id', ','.$this->_userId.','], ['=', 'user_id', ''], ['=', 'user_id', $this->_userId]])
            ->where(['a.user_id' => $this->_userId, 'b.category_id' => '0'])
            ->orderBy('a.notice_id desc')
            ->asArray()
            ->one();

        if ($sysNotice)
        {
            if ($sysNotice['notice_time'] > $stime)
            {
                $sysNotice['notice_time'] = date('H:i', $sysNotice['notice_time']);
            }
            else
            {
                $sysNotice['notice_time'] = date('n/d', $sysNotice['notice_time']);
            }

        }
        $data['sys_notice'] = $sysNotice;

        // 分销消息通知
        $distributionNotice = UserNoticeRelation::find()
            ->alias('a')
            ->select(['a.notice_id', 'b.category_id', 'b.title', 'b.notice_time'])
            ->leftJoin(Notice::tableName(). ' b', 'a.notice_id = b.notice_id')
            ->where(['a.user_id' => $this->_userId, 'b.category_id' => '1'])
            ->orderBy('a.notice_id desc')
            ->asArray()
            ->one();
        if ($distributionNotice)
        {
            if ($distributionNotice['notice_time'] > $stime)
            {
                $distributionNotice['notice_time'] = date('H:i', $distributionNotice['notice_time']);
            }
            else
            {
                $distributionNotice['notice_time'] = date('n/d', $distributionNotice['notice_time']);
            }
        }
        $data['distribution_notice'] = $distributionNotice;

        // 奖励通知
        $rewardNotice = UserNoticeRelation::find()
            ->alias('a')
            ->select(['a.notice_id', 'b.category_id', 'b.title', 'b.notice_time'])
            ->leftJoin(Notice::tableName(). ' b', 'a.notice_id = b.notice_id')
            ->where(['a.user_id' => $this->_userId, 'b.category_id' => '2'])
            ->orderBy('a.notice_id desc')
            ->asArray()
            ->one();
        if ($rewardNotice)
        {
            if ($rewardNotice['notice_time'] > $stime)
            {
                $rewardNotice['notice_time'] = date('H:i', $rewardNotice['notice_time']);
            }
            else
            {
                $rewardNotice['notice_time'] = date('n/d', $rewardNotice['notice_time']);
            }
        }
        $data['reward_notice'] = $rewardNotice;

        return response($data);

    }

    /**
     * 消息通知列表
     * @return array
     */
    public function actionList()
    {
        $categoryId = Yii::$app->request->get('category_id', 0);
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = UserNoticeRelation::find()
            ->alias('a')
            ->select(['a.notice_id', 'b.title', 'b.sub_category_id', 'b.notice_time'])
            ->leftJoin(Notice::tableName() . ' b', 'a.notice_id = b.notice_id')
            ->where(['a.user_id' => $this->_userId, 'b.category_id' => $categoryId])
            ->orderBy('a.notice_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['notice_time'] = date('Y-m-d', $v['notice_time']);
            $v['title_type'] = Yii::$app->params['sub_notice_category'][$categoryId][$v['sub_category_id']];
            $model[$k] = $v;
        }

        return response($model);
    }

    /**
     * 通知详情
     * @return array
     */
    public function actionInfo()
    {
        $noticeId = Yii::$app->request->get('notice_id', 0);
        $model = Notice::find()
            ->where(['notice_id' => $noticeId])
            ->asArray()
            ->one();

        if ($model)
        {
            $model['notice_time'] = date('Y-m-d', $model['notice_time']);
            $model['title_type'] = Yii::$app->params['sub_notice_category'][$model['category_id']][$model['sub_category_id']];
        }

        return response($model);
    }

    /**
     * 用户拉取消息
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionPull()
    {
        $response = ['has_notice' => 0]; // 是否有新消息

        if (!$this->_userId)
        {
            return response($response);
        }
        // 查询用户最后一条消息
        $uNotice = UserNoticeRelation::find()
            ->where(['user_id' => $this->_userId])
            ->orderBy('user_notice_relation_id desc')
            ->asArray()
            ->one();
        $noticeId = $uNotice ? $uNotice['notice_id'] : 0;

        $notice = Notice::find()
            ->select(['notice_id'])
            ->where(['or', ['like', 'user_id', ','.$this->_userId.','], ['=', 'user_id', ''], ['=', 'user_id', $this->_userId]])
            ->andWhere(['>', 'notice_id', $noticeId])
            ->asArray()
            ->all();

        if ($notice)
        {
            $data = [];
            foreach ($notice as $v)
            {
                $data[] = ['notice_id' => $v['notice_id'], 'user_id' => $this->_userId];
            }
            Yii::$app->db->createCommand()
                ->batchInsert(UserNoticeRelation::tableName(),['notice_id','user_id'], $data)
                ->execute();

            $response['has_notice'] = 1;
        }

        return response($response);
    }
}
