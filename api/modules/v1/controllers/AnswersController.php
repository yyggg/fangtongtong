<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;

use Yii;
use common\models\PropertiesAnswers;
use api\controllers\BaseCotroller;

class AnswersController extends BaseCotroller
{
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
