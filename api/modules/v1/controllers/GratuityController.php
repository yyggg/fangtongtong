<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-27 21:26
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseController;
use api\models\User;
use common\models\UserArticleGratuity;
use Yii;

class GratuityController extends BaseController
{
    /**
     * 打赏列表
     * @return array
     */
    public function actionIndex()
    {
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = UserArticleGratuity::find()
            ->alias('a')
            ->select([
                'a.money',
                'a.create_time',
                'b.nickname',
                'b.headimgurl'
            ])
            ->leftJoin(User::tableName() . ' b', 'a.user_id = b.user_id')
            ->where(['a.art_user_id' => $this->_userId, 'status' => 2])
            ->orderBy('a.user_article_gratuity_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['create_time'] = date('Y.m.d H:i', $v['create_time']);
            $model[$k] = $v;
        }

        $totalMoney = UserArticleGratuity::find()
            ->where(['art_user_id' => $this->_userId])
            ->sum('money');

        return response([
            'list' => $model,
            'total_money' => $totalMoney
        ]);
    }
}