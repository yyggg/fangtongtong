<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;


use common\models\Comment;
use common\models\Properties;
use common\models\PropertiesLabel;
use common\models\PropertiesLabelRelation;
use common\models\Region;
use common\models\User;
use Yii;
use api\controllers\BaseCotroller;

class CommentController extends BaseCotroller
{
    /**
     * 楼盘点评列表
     * @return array
     */
    public function actionIndex()
    {
        $propertiesId = Yii::$app->request->get('properties_id', 0);
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = Comment::find()
            ->alias('a')
            ->select(['a.comment_id','a.content', 'a.create_time', 'b.nickname', 'b.headimgurl'])
            ->leftJoin(User::tableName() . ' b', 'a.user_id = b.user_id')
            ->where(['a.properties_id' => $propertiesId])
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['create_time'] = date('Y.m.d H:i:s');
            $model[$k] = $v;
        }

        return response($model);
    }

    /**
     * 获取楼盘点评详情
     * @return array
     */
    public function actionInfo()
    {
        $commentId = Yii::$app->request->get('comment_id', 0);

        $model = Comment::find()
            ->alias('a')
            ->select([
                'a.comment_id','a.content', 'a.create_time', 'b.headimgurl', 'b.nickname', 'c.name', 'c.pic', 'c.sale_status',
                'c.price_metre', 'd.region_name','GROUP_CONCAT(DISTINCT f.label_name) AS label_name'
            ])
            ->leftJoin(User::tableName() . ' b', 'a.user_id = b.user_id')
            ->leftJoin(Properties::tableName() . ' c', 'a.properties_id = c.properties_id')
            ->leftJoin(Region::tableName() . ' d', 'c.region_code = d.region_code')
            ->leftJoin(PropertiesLabelRelation::tableName() . ' e', 'a.properties_id = e.properties_id')
            ->leftJoin(PropertiesLabel::tableName() . ' f', 'e.properties_label_id = f.properties_label_id')
            ->where(['a.comment_id' => $commentId])
            ->groupBy('a.comment_id')
            ->asArray()
            ->one();

        if ($model)
        {
            $model['create_time'] = date('Y.m.d H:i:s');
            $model['sale_status'] = Yii::$app->params['sale_status'][$model['sale_status']];
        }

        return response($model);
    }
}
