<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;

use common\models\Properties;
use common\models\PropertiesAsk;
use Yii;
use common\models\PropertiesAnswers;
use api\controllers\BaseController;

class AnswersController extends BaseController
{

    /**
     * 我的回答列表
     * @return array
     */
    public function actionUserIndex()
    {
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = PropertiesAnswers::find()
            ->alias('a')
            ->select([
                'a.properties_ask_id',
                'a.content',
                'a.create_time',
                'b.title',
                'c.name',
                'c.properties_id'
            ])
            ->leftJoin(PropertiesAsk::tableName() . ' b', 'a.properties_ask_id = b.properties_ask_id')
            ->leftJoin(Properties::tableName() . ' c', 'b.properties_id = c.properties_id')
            ->where(['a.answers_user_id' => $this->_userId])
            ->orderBy('a.properties_answers_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['create_time'] = date('Y.m.d H:i', $v['create_time']);
            $v['answers_count'] = PropertiesAnswers::find()
                ->where(['properties_ask_id' => $v['properties_ask_id']])
                ->count(1);
            $model[$k] = $v;
        }

        return response($model);
    }
    /**
     * 回答
     * @return array
     */
    public function actionCreate()
    {
        $askId = Yii::$app->request->post('properties_ask_id', 0);
        $userId = Yii::$app->request->post('user_id', 0);
        $content = Yii::$app->request->post('content', '');

        $model = new  PropertiesAnswers();
        $model->content = $content;
        $model->answers_user_id = $userId;
        $model->properties_ask_id = $askId;
        $model->create_time = time();

        if (!$model->save())
        {
            return response([], '20001');
        }
        return response();
    }
}
