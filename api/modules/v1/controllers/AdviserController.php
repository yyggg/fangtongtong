<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;

use common\models\ApplyAdviser;
use common\models\Comment;
use common\models\Properties;
use common\models\ServeScope;
use common\models\ServeScopeRelation;
use common\models\UserAdviserExt;
use common\models\UserArticle;
use api\models\User;
use crazyfd\qiniu\Qiniu;
use Yii;
use common\models\PropertiesAdviserRelation;
use api\controllers\BaseController;

class AdviserController extends BaseController
{
    /**
     * 楼盘顾问列表
     * @return array
     */
    public function actionIndex()
    {

        $propertiesId = Yii::$app->request->get('properties_id', 0);

        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = PropertiesAdviserRelation::find()
            ->alias('a')
            ->select(['b.user_id', 'b.headimgurl', 'b.nickname', 'b.is_adviser', 'b.phone'])
            ->leftJoin(User::tableName() . ' b', 'a.user_id = b.user_id')
            ->andWhere(['properties_id' => $propertiesId])
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();


        return response($model);
    }

    /**
     * 优质顾问列表
     * @return array
     */
    public function actionGoodList()
    {
        $keyword = Yii::$app->request->post('keyword', '');
        $regionCode = Yii::$app->request->post('region_code', '');

        $serveScopeId = Yii::$app->request->post('serve_scope_id', '');

        if ($serveScopeId)
        {
            $serveScopeId = explode(',', $serveScopeId);
        }


        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = Properties::find()
            ->alias('a')
            ->select([
                'a.name', 'c.user_id', 'c.headimgurl', 'c.nickname', 'c.is_adviser', 'c.phone', 'd.serve_people',
                'e.user_article_id', 'e.title'
            ])
            ->leftJoin(PropertiesAdviserRelation::tableName() . ' b', 'a.properties_id = b.properties_id')
            ->innerJoin(User::tableName() . ' c', 'b.user_id = c.user_id')
            ->leftJoin(UserAdviserExt::tableName() . ' d', 'b.user_id = d.user_id')
            ->leftJoin(UserArticle::tableName() . ' e', 'b.user_id = e.user_id')
            ->leftJoin(ServeScopeRelation::tableName() . ' f', 'd.user_id = f.user_id')
            ->andFilterWhere(['or',['like','a.name', $keyword], ['like','c.nickname', $keyword]])
            ->andFilterWhere(['in', 'f.serve_scope_id', $serveScopeId])
            ->andFilterWhere(['d.region_code' => $regionCode])
            ->groupBy('c.user_id') // 去重复
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();


        return response($model);
    }


    /**
     * 顾问首页
     * @return array
     */
    public function actionHome()
    {
        $userId = Yii::$app->request->get('user_id', 0);

        $user = User::find()
            ->alias('a')
            ->select([
                'a.nickname', 'a.headimgurl', 'a.is_adviser', 'b.serve_people', 'b.serve_length', 'b.overall_score'
            ])
            ->leftJoin(UserAdviserExt::tableName() . ' b', 'a.user_id = b.user_id')
            ->where(['a.user_id' => $userId])
            ->asArray()
            ->one();

        $user['serve_length'] = floatval($user['serve_length']);

        // 服务范围
        $serve = ServeScopeRelation::find()
            ->alias('a')
            ->select(['b.serve_name'])
            ->leftJoin(ServeScope::tableName() . ' b', 'a.serve_scope_id = b.serve_scope_id')
            ->where(['user_id' => $userId])
            ->asArray()
            ->all();

        // 文章
        $article = UserArticle::find()
            ->select(['title', 'user_article_id', 'create_time'])
            ->where(['user_id' => $userId])
            ->orderBy('user_article_id desc')
            ->asArray()
            ->limit(2)
            ->all();
        foreach ($article as $k => $v)
        {
            $v['create_time'] = date('Y.m.d H:i:s', $v['create_time']);
            $article[$k] = $v;
        }

        // 点评
        $comment = Comment::find()
            ->alias('a')
            ->select([
                'a.content','a.create_time','a.comment_id','b.name','b.properties_id'
            ])
            ->leftJoin(Properties::tableName() . ' b', 'a.properties_id = b.properties_id')
            ->where(['a.user_id' => $userId])
            ->orderBy('a.comment_id desc')
            ->limit(2)
            ->asArray()
            ->all();


        foreach ($comment as $k => $v)
        {
            $v['create_time'] = date('Y.m.d H:i:s', $v['create_time']);
            $comment[$k] = $v;
        }

        $data['user'] = $user;
        $data['serve_scope'] = $serve;
        $data['article'] = $article;
        $data['comment'] = $comment;

        return response($data);
    }


    /**
     * 顾问文章列表
     * @return array
     */
    public function actionArticleList()
    {
        $userId = Yii::$app->request->get('user_id', 0);
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = UserArticle::find()
            ->alias('a')
            ->select(['a.user_article_id', 'a.title', 'a.star', 'a.comment', 'a.gratuity', 'a.create_time'])
            ->leftJoin(User::tableName() . ' b', 'a.user_id = b.user_id')
            ->where(['a.user_id' => $userId])
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['create_time'] = date('  Y.m.d H:i:s');
            $model[$k] = $v;
        }

        return response($model);
    }

    /**
     * 顾问文章详情
     * @return array
     */
    public function actionArticleInfo()
    {
        $articleId = Yii::$app->request->get('user_article_id', 0);

        $model = UserArticle::find()
            ->alias('a')
            ->select(['a.user_id', 'a.title', 'a.create_time', 'a.content', 'b.nickname', 'b.headimgurl', 'b.phone'])
            ->leftJoin(User::tableName() . ' b', 'a.user_id = b.user_id')
            ->where(['user_article_id' => $articleId])
            ->asArray()
            ->one();
        if ($model)
        {
            $model['create_time'] = date('Y.m.d H:i:s');
        }

        return response($model);
    }

    /**
     * 顾问点评列表
     * @return array
     */
    public function actionCommentList()
    {
        $userId = Yii::$app->request->get('user_id', 0);
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $comment = Comment::find()
            ->alias('a')
            ->select([
                'a.content','a.create_time','a.comment_id','b.name','b.properties_id'
            ])
            ->leftJoin(Properties::tableName() . ' b', 'a.properties_id = b.properties_id')
            ->where(['a.user_id' => $userId])
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($comment as $k => $v)
        {
            $v['create_time'] = date('Y.m.d H:i:s');
            $comment[$k] = $v;
        }

        return response($comment);
    }

    /**
     * 顾问点评详情
     * @return array
     */
    public function actionCommentInfo()
    {
        $commentId = Yii::$app->request->get('comment_id', 0);

        $model = Comment::find()
            ->alias('a')
            ->select([
                'a.user_id', 'a.create_time', 'a.content', 'b.nickname', 'b.headimgurl', 'b.phone',
                'c.name', 'c.pic', 'c.properties_id','c.sale_status'
            ])
            ->leftJoin(User::tableName() . ' b', 'a.user_id = b.user_id')
            ->leftJoin(Properties::tableName() . ' c', 'a.properties_id = c.properties_id')
            ->where(['a.comment_id' => $commentId])
            ->asArray()
            ->one();
        if ($model)
        {
            $model['create_time'] = date('Y.m.d H:i:s');
            $model['sale_status'] = Yii::$app->params['sale_status'][$model['sale_status']];
        }

        return response($model);
    }

    /**
     * 升级顾问详情
     * @return array
     */
    public function actionApplyInfo()
    {
        $user  =  User::find()
            ->alias('a')
            ->select(['a.user_id', 'a.is_adviser', 'b.expire_time'])
            ->leftJoin(UserAdviserExt::tableName() . ' b', 'a.user_id = b.user_id')
            ->where(['a.user_id' => $this->_userId])
            ->asArray()
            ->one();

        if ($user['expire_time'])
        {
            $user['expire_time'] = date('Y.m.d', $user['expire_time']);
        }

        $applyInfo = ApplyAdviser::find()
            ->where(['user_id' => $this->_userId])
            ->asArray()
            ->one();

        return response([
            'user' => $user,
            'apply_info' => $applyInfo,
        ]);
    }

    /**
     * 顾问升级申请
     * @return array
     * @throws \Exception
     */
    public function actionApply()
    {
        $name = Yii::$app->request->post('name', '');
        $idcard = Yii::$app->request->post('idcard', '');

        $fileName = uniqid().time();

        $model = ApplyAdviser::findOne(['user_id' => $this->_userId]);

        if (!$model)
        {
            $model = new ApplyAdviser();
            $model->user_id = $this->_userId;
        }

        $qiniu = new Qiniu(Yii::$app->params['access_key'],Yii::$app->params['secret_key'],Yii::$app->params['domain'],Yii::$app->params['bucket']);

        $qiniu->uploadFile($_FILES['picture']['tmp_name'], $fileName);

        $url = $qiniu->getLink($fileName);

        $model->name = $name;
        $model->idcard = $idcard;
        $model->picture = $url;

        if ($model->save())
        {
            return response();
        }

        return response([], '20001');
    }

}
