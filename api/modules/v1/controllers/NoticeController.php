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
     * 消息通知主页
     * @return array
     */
    public function actionIndex()
    {
        $data = [];
        $userId = Yii::$app->request->get('user_id', 0);

        // 系统类型通知
        $sysNotice = Notice::find()
            ->select(['notice_id', 'category_id', 'title', 'notice_time'])
            ->where(['or', ['=', 'user_id', $userId], ['=', 'user_id', '0']])
            ->andWhere(['category_id' => '0'])
            ->orderBy('notice_id desc')
            ->asArray()
            ->one();

        if ($sysNotice)
        {
            $sysNotice['notice_time'] = date('n/d', $sysNotice['notice_time']);
        }
        $data['sys_notice'] = $sysNotice;

        // 分销消息通知
        $distributionNotice = Notice::find()
            ->select(['notice_id', 'category_id', 'title', 'notice_time'])
            ->where(['or', ['=', 'user_id', $userId], ['=', 'user_id', '0']])
            ->andWhere(['category_id' => '1'])
            ->orderBy('notice_id desc')
            ->asArray()
            ->one();
        if ($distributionNotice)
        {
            $distributionNotice['notice_time'] = date('n/d', $distributionNotice['notice_time']);
        }
        $data['distribution_notice'] = $distributionNotice;

        // 奖励通知
        $rewardNotice = Notice::find()
            ->select(['notice_id', 'category_id', 'title', 'notice_time'])
            ->where(['or', ['=', 'user_id', $userId], ['=', 'user_id', '0']])
            ->andWhere(['category_id' => '2'])
            ->orderBy('notice_id desc')
            ->asArray()
            ->one();
        if ($rewardNotice)
        {
            $rewardNotice['notice_time'] = date('n/d', $rewardNotice['notice_time']);
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
        $userId = Yii::$app->request->get('user_id', 0);
        $categoryId = Yii::$app->request->get('category_id', 0);
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = Notice::find()
            ->select(['notice_id', 'title', 'sub_category_id', 'notice_time'])
            ->where(['or', ['=', 'user_id', $userId], ['=', 'user_id', '0']])
            ->andWhere(['category_id' => $categoryId])
            ->orderBy('notice_id desc')
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
}
