<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;

use Yii;
use common\models\Properties;
use common\models\PropertiesAnswers;
use common\models\PropertiesAsk;
use common\models\User;
use api\controllers\BaseController;

class AskController extends BaseController
{
    /**
     * 楼盘问答列表
     * @return array
     */
    public function actionIndex()
    {
        $data = [];
        $propertiesId = Yii::$app->request->get('properties_id', 0);
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $ask = PropertiesAsk::find()
            ->where(['properties_id' => $propertiesId])
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->indexBy('properties_ask_id')
            ->asArray()
            ->all();

        foreach ($ask as $k => $v)
        {
            $data[$v['properties_ask_id']] = PropertiesAnswers::find()
                ->alias('a')
                ->select(['a.content', 'a.create_time', 'b.is_adviser','b.nickname', 'b.headimgurl','COUNT(1) AS count'])
                ->where(['a.properties_ask_id' => $v['properties_ask_id']])
                ->leftJoin(User::tableName() . ' b', 'a.answers_user_id = b.user_id')
                ->orderBy('a.create_time desc')
                ->asArray()
                ->one();
            $data[$v['properties_ask_id']]['title'] = $v['title'];
        }


        foreach ($data as $k => $v)
        {
            $v['create_time'] = date('Y.m.d H:i:s', $v['create_time']);
            $data[$k] = $v;
        }


        return response($data);
    }

    /**
     * 我的提问列表
     * @return array
     */
    public function actionUserIndex()
    {
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = PropertiesAsk::find()
            ->alias('a')
            ->select([
                'a.properties_ask_id',
                'a.title',
                'a.create_time',
                'b.name',
                'a.properties_id',
                'count(c.properties_ask_id) count'
            ])
            ->leftJoin(Properties::tableName() . ' b', 'a.properties_id = b.properties_id')
            ->leftJoin(PropertiesAnswers::tableName() . ' c', 'a.properties_ask_id = c.properties_ask_id')
            ->where(['a.ask_user_id' => $this->_userId])
            ->groupBy('a.properties_ask_id')
            ->orderBy('a.properties_ask_id desc')
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

    /**
     * 获取楼盘问答详情
     * @return array
     */
    public function actionInfo()
    {
        $data = [];
        $askId = Yii::$app->request->get('properties_ask_id', 0);

        $ask = PropertiesAsk::find()
            ->alias('a')
            ->select(['a.title', 'b.nickname', 'a.create_time', 'c.name', 'c.properties_id'])
            ->leftJoin(User::tableName() . ' b', 'a.ask_user_id = b.user_id')
            ->leftJoin(Properties::tableName() . ' c', 'c.properties_id = a.properties_id')
            ->where(['a.properties_ask_id' => $askId])
            ->asArray()
            ->one();

        $ask['create_time'] =  date('Y.m.d H:i:s', $ask['create_time']);

        $model = PropertiesAnswers::find()
            ->alias('a')
            ->select([
                'a.content', 'a.create_time', 'b.headimgurl', 'b.nickname'
            ])
            ->leftJoin(User::tableName() . ' b', 'b.user_id = a.answers_user_id')
            ->where(['a.properties_ask_id' => $askId])
            ->orderBy('a.properties_answers_id desc')
            ->asArray()
            ->all();

        if ($model)
        {
            foreach ($model as $k => $v)
            {
                $model[$k]['create_time'] = date('Y.m.d H:i:s', $v['create_time']);
            }

        }

        $data['ask'] = $ask;
        $data['answers'] = $model;

        return response($data);
    }

    /**
     * 提问
     * @return array
     */
    public function actionCreate()
    {
        $propertiesId = Yii::$app->request->post('properties_id', 0);
        $userId = Yii::$app->request->post('user_id', 0);
        $title = Yii::$app->request->post('title', '');

        $model = new  PropertiesAsk();
        $model->title = $title;
        $model->ask_user_id = $userId;
        $model->properties_id = $propertiesId;
        $model->create_time = time();

        if (!$model->save())
        {
            return response([], '20001');
        }
        return response();
    }
}
