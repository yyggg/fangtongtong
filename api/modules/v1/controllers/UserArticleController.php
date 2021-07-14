<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-27 20:04
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseController;
use api\models\User;
use common\models\UserArticle;
use common\models\UserArticleGratuity;
use Yii;

class UserArticleController extends BaseController
{
    /**
     * 我的文章
     * @return array
     */
    public function actionIndex()
    {
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = UserArticle::find()
            ->select([
                'user_article_id', 'title', 'star', 'comment', 'gratuity'
            ])
            ->where(['user_id' => $this->_userId])
            ->orderBy('user_article_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        return response($model);
    }

    /**
     * 文章详情
     * @return array
     */
    public function actionInfo()
    {
        $artId = Yii::$app->request->get('user_article_id', 0);

        $art =  UserArticle::find()
            ->select(['title', 'content', 'star', 'create_time'])
            ->where(['user_article_id' => $artId])
            ->asArray()
            ->one();
        $art['create_time'] = date('Y.m.d H:i', $art['create_time'] );

        $gratuity = UserArticleGratuity::find()
            ->alias('a')
            ->select([
                'a.user_id',
                'b.headimgurl'
            ])
            ->leftJoin(User::tableName() . ' b', 'a.user_id = b.user_id')
            ->where(['a.art_user_id' => $this->_userId, 'a.status' => 2])
            ->orderBy('a.user_article_gratuity_id desc')
            ->asArray()
            ->all();

        $art['gratuity_list'] = $gratuity;

        return response($art);
    }

    /**
     * 创建文章
     * @return array
     */
    public function actionCreate()
    {
        $params = Yii::$app->request->post();
        $params['user_id'] = $this->_userId;

        $model = new UserArticle();

        if ($model->load(['UserArticle' => $params]) && $model->save())
        {
            return response();
        }
        return response([], '30030', $model->getErrorSummary(false)[0]);
    }
}
