<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-28 10:18
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseController;
use api\models\User;
use common\models\HouseType;
use common\models\Properties;
use common\models\PropertiesAdviserRelation;
use common\models\SubscribeProperties;
use common\models\SubscribeUser;
use common\models\UserAdviserExt;
use Yii;

class SubscribeController extends BaseController
{
    /**
     * 用户关注的楼盘
     * @return array
     */
    public function actionProperties()
    {
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = SubscribeProperties::find()
            ->alias('a')
            ->select([
                'a.properties_id',
                'a.create_time',
                'b.name',
                'b.pic',
                'b.sale_status',
                'b.price_metre',
                'min(c.square_metre) as min_square_metre',
                'max(c.square_metre) as max_square_metre'
            ])
            ->leftJoin(Properties::tableName() . ' b', 'a.properties_id = b.properties_id')
            ->leftJoin(HouseType::tableName() . ' c', 'b.properties_id = c.properties_id')
            ->where(['a.user_id' => $this->_userId, 'a.status' => 1])
            ->groupBy('a.properties_id')
            ->orderBy('a.subscribe_properties_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['create_time'] = date('Y.m.d', $v['create_time']);
            $v['sale_status'] = Yii::$app->params['sale_status'][$v['sale_status']];
            if ($v['pic'])
            {
                $v['pic'] = json_decode($v['pic']);
            }
            $model[$k] = $v;
        }

        return response($model);
    }

    /**
     * 关注楼盘
     * @return array
     */
    public function actionSubProperties() {
        $propertiesId = Yii::$app->request->post('properties_id', 0);
        if ($propertiesId) {
            $model = SubscribeProperties::findOne(['properties_id' => $propertiesId, 'user_id' => $this->_userId]);
            if (!$model) {
                $model = new SubscribeProperties();
                $model->user_id = $this->_userId;
                $model->status = 1;
                $model->properties_id = $propertiesId;
                $model->save(false);
            }
            else {
            	if($model->status == 0) {
            		$model->status = 1;
            	}else {
            		$model->status = 0;	
            	}
                $model->save(false);
            }
        }

        return response();
    }

    /**
     * 关注的顾问
     * @return array
     */
    public function actionUser()
    {
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = SubscribeUser::find()
            ->alias('a')
            ->select([
                'b.nickname',
                'b.headimgurl',
                'b.phone',
                'b.user_id',
                'b.is_adviser',
                'c.serve_people',
                'e.properties_id',
                'e.name',
            ])
            ->leftJoin(User::tableName() . ' b', 'a.adviser_user_id = b.user_id')
            ->leftJoin(UserAdviserExt::tableName() . ' c', 'b.user_id = c.user_id')
            ->leftJoin(PropertiesAdviserRelation::tableName() . ' d', 'a.adviser_user_id = d.user_id')
            ->leftJoin(Properties::tableName() . ' e', 'd.properties_id = e.properties_id')
            ->where(['a.user_id' => $this->_userId])
            ->orderBy('a.subscribe_user_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        return response($model);
    }
}
