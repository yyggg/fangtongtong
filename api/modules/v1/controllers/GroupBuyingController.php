<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2019-12-31 19:40
 */
namespace api\modules\v1\controllers;

use common\models\GroupBuyingTpl;
use common\models\JoinGroupBuying;
use common\models\Properties;
use common\models\User;
use Yii;
use api\controllers\BaseCotroller;
use common\models\GroupBuying;


class GroupBuyingController extends BaseCotroller
{
    /**
     * 获取拼团活动列表
     * @return array
     */
    public function actionIndex()
    {
        $propertiesId = Yii::$app->request->get('properties_id', '');
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = GroupBuying::find()
            ->alias('a')
            ->select(['a.group_buying_id', 'b.title', 'b.people','b.s_time', 'b.e_time', 'b.remark', 'c.name', 'c.pic'])
            ->leftJoin(GroupBuyingTpl::tableName() . ' b', 'a.group_buying_tpl_id = b.group_buying_tpl_id')
            ->leftJoin(Properties::tableName() . ' c', 'a.properties_id = c.properties_id')
            ->where(['>', 'b.e_time', time()])
            ->andWhere(['a.status' => '0'])
            ->filterWhere(['a.properties_id' => $propertiesId])
            ->orderBy('a.group_buying_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        return response($model);
    }

    /**
     * 参加拼团
     * @return array
     */
    public function actionJoin()
    {
        $groupBuyingId = Yii::$app->request->post('group_buying_id', 0);
        $userId = Yii::$app->request->post('user_id', 0);

        $groupBuyingInfo = GroupBuying::find()
            ->select([
                'a.group_buying_id', 'a.properties_id', 'a.status', 'b.e_time','b.people'
            ])
            ->alias('a')
            ->leftJoin(GroupBuyingTpl::tableName() . ' b', 'a.group_buying_tpl_id = b.group_buying_tpl_id')
            ->where(['a.group_buying_id' => $groupBuyingId])
            ->one();

        // 取不到拼团信息 或 取不到用户ID 或 拼团已经结束或成功 或 拼团结束日期过期 视无法参加
        if (!$groupBuyingInfo || !$userId || $groupBuyingInfo->status > 0 || $groupBuyingInfo->e_time < time())
        {
            return response([], '20001');
        }

        // 用户拼团中或拼团已成功 视无法参加
        $joinGroupBuyingInfo = JoinGroupBuying::find()
            ->where([
                'user_id' => $userId,
                'is_cancel' => '0',
                'properties_id' => $groupBuyingInfo->properties_id
            ])
            ->one();

        if ($joinGroupBuyingInfo)
        {
            return response([], '20001');
        }

        // 统计已参加人数
        $count = JoinGroupBuying::find()
            ->where(['group_buying_id' => $groupBuyingId, 'is_cancel' => '0'])
            ->count(1);
        // 拼团人数已超 视无法参加
        if ($count >= $groupBuyingInfo->people)
        {
            return response([], '20001');
        }

        $model = new JoinGroupBuying();
        $model->user_id = $userId;
        $model->group_buying_id = $groupBuyingId;
        $model->properties_id = $groupBuyingInfo->properties_id;
        $model->join_time = time();

        // 满团
        if (($count + 1) >= $groupBuyingInfo->people)
        {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 插入记录
                $model->save();
                // 更新状态
                $groupBuyingInfo->status = 1;
                $groupBuyingInfo->save();

                $transaction->commit();

            } catch (\Exception $e) {

                $transaction->rollBack();
                return response([], '20001');
            }
        }
        else
        {
            if (!$model->save()){
                return response([], '20001');
            }
        }

        return response();

    }

    /**
     * 拼团详情
     * @return array
     */
    public function actionInfo()
    {
        $data = [];
        $groupBuyingId = Yii::$app->request->get('group_buying_id', 0);

        $groupBuyingInfo = GroupBuying::find()
            ->alias('a')
            ->select([
                'a.properties_id', 'a.group_buying_tpl_id', 'b.s_time', 'b.e_time', 'b.people', 'b.title', 'c.name', 'c.pic'
            ])
            ->leftJoin(GroupBuyingTpl::tableName() . ' b', 'a.group_buying_tpl_id = b.group_buying_tpl_id')
            ->leftJoin(Properties::tableName() . ' c', 'a.properties_id = c.properties_id')
            ->where(['a.group_buying_id' => $groupBuyingId])
            ->asArray()
            ->one();


        $groupBuyingInfo['countdown_time'] = $groupBuyingInfo['e_time'] - time();
        $groupBuyingInfo['s_time'] = date('Y.m.d', $groupBuyingInfo['s_time']);
        $groupBuyingInfo['e_time'] = date('Y.m.d', $groupBuyingInfo['e_time']);
        $data['info'] = $groupBuyingInfo;

        $data['user'] = JoinGroupBuying::find()
            ->select(['b.headimgurl'])
            ->alias('a')
            ->leftJoin(User::tableName() . ' b', 'a.user_id = b.user_id')
            ->where(['group_buying_id' => $groupBuyingId, 'a.is_cancel' => '0'])
            ->asArray()
            ->all();

        return response($data);
    }

    /**
     * 必看-拼团详情
     * @return array
     */
    public function actionInfo2()
    {
        $data = [];
        $groupBuyingId = Yii::$app->request->get('group_buying_id', 0);
        $userId = Yii::$app->request->get('user_id', 0);

        // 活动详情
        $groupBuyingInfo = GroupBuying::find()
            ->alias('a')
            ->select([
                'a.group_buying_id','b.title','b.remark','b.rule', 'b.s_time','b.e_time','b.people','a.status',
                'a.properties_id','a.user_id','a.group_buying_tpl_id', 'c.name', 'c.pic'
            ])
            ->leftJoin(GroupBuyingTpl::tableName() . ' b', 'a.group_buying_tpl_id = b.group_buying_tpl_id')
            ->leftJoin(Properties::tableName() . ' c', 'a.properties_id = c.properties_id')
            ->where(['a.group_buying_id' => $groupBuyingId])
            ->asArray()
            ->one();

        $groupBuyingInfo['countdown_time'] = $groupBuyingInfo['e_time'] - time();
        $groupBuyingInfo['s_time'] = date('Y.m.d', $groupBuyingInfo['s_time']);
        $groupBuyingInfo['e_time'] = date('Y.m.d', $groupBuyingInfo['e_time']);
        $data['info'] = $groupBuyingInfo;

        // 当前活动参数用户
        $data['user'] = JoinGroupBuying::find()
            ->select(['b.headimgurl'])
            ->alias('a')
            ->leftJoin(User::tableName() . ' b', 'a.user_id = b.user_id')
            ->where(['a.is_cancel' => '0'])
            ->asArray()
            ->all();

        // 查询用户参与过本楼盘的活动
        $userJoin = JoinGroupBuying::find()
            ->select(['join_group_buying_id'])
            ->where(['properties_id' => $groupBuyingInfo['properties_id'], 'is_cancel' => '0', 'user_id' => $userId])
            ->indexBy('group_buying_id')
            ->asArray()
            ->all();


        // 当前楼盘所有活动
        $list = GroupBuying::find()
            ->alias('a')
            ->select([
                'a.group_buying_id','b.title','b.s_time','b.e_time','b.people','a.user_id'
            ])
            ->leftJoin(GroupBuyingTpl::tableName() . ' b', 'a.group_buying_tpl_id = b.group_buying_tpl_id')
            ->where(['a.properties_id' => $groupBuyingInfo['properties_id'], 'a.status' => '0'])
            ->andWhere(['>', 'b.e_time', time()])
            ->asArray()
            ->all();
        if ($list)
        {
            foreach ($list as $k => $v)
            {
                $v['pic'] = $groupBuyingInfo['pic'];
                $v['name'] = $groupBuyingInfo['name'];
                $v['s_time'] = date('Y.m.d', $v['s_time']);
                $v['e_time'] = date('Y.m.d', $v['e_time']);
                $v['is_join'] = isset($userJoin[$v['group_buying_id']]) ? 1 : 0;
                $list[$k] = $v;
            }
        }
        $data['list'] = $list;

        return response($data);
    }


    public function actionCreate()
    {
        $userId = Yii::$app->request->post('user_id', 0);
        $propertiesId = Yii::$app->request->post('properties_id', 0);
        $tplId = Yii::$app->request->post('group_buying_tpl_id', 0);

        $groupBuying = GroupBuying::find()
            ->alias('a')
            ->leftJoin(GroupBuyingTpl::tableName() . ' b', 'a.group_buying_tpl_id = b.group_buying_tpl_id')
            ->where(['a.user_id' => $userId, 'a.properties_id' => $propertiesId, 'a.status' => '0'])
            ->andWhere(['>', 'b.e_time', time()])
            ->asArray()
            ->one();

        // 已经开有团
        if (!$tplId || !$propertiesId || $groupBuying)
        {
            return response([], '20001');
        }

        $model = new GroupBuying();
        $model->user_id = $userId;
        $model->properties_id = $propertiesId;
        $model->group_buying_tpl_id = $tplId;
        $model->create_time = time();
        if (!$model->save())
        {
            return response([], '20001');
        }

        return response();
    }

}
