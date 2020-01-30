<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;

use common\models\AppointSeeHouse;
use common\models\HouseType;
use common\models\Properties;
use common\models\PropertiesLabel;
use common\models\PropertiesLabelRelation;
use common\models\Region;
use Yii;
use api\controllers\BaseController;

class SeeHouseController extends BaseController
{
    /**
     * 我的预约
     * @return array
     */
    public function actionIndex()
    {
        $model = AppointSeeHouse::find()
            ->alias('a')
            ->select([
                'a.appoint_see_room_id',
                'a.status',
                'a.date',
                'a.time_slot',
                'b.name',
                'b.address'
            ])
            ->leftJoin(Properties::tableName() . ' b', 'a.properties_id = b.properties_id')
            ->where(['user_id' => $this->_userId])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['date'] = date('Y-m-d', $v['date']);
            $model[$k] = $v;
        }
        return response($model);
    }

    public function actionInfo()
    {
        $id = Yii::$app->request->get('appoint_see_room_id', 0);

        $model = AppointSeeHouse::find()
            ->alias('a')
            ->select([
                'a.status',
                'a.date',
                'a.time_slot',
                'b.name',
                'b.pic',
                'b.price_metre',
                'b.properties_id',
                'c.region_name',
                'b.sale_status'
            ])
            ->leftJoin(Properties::tableName() . ' b', 'a.properties_id = b.properties_id')
            ->leftJoin(Region::tableName() . ' c', 'b.region_code = c.region_code')
            ->where(['appoint_see_room_id' => $id])
            ->asArray()
            ->one();

        $label = PropertiesLabelRelation::find()
            ->select(['label_name'])
            ->alias('a')
            ->leftJoin(PropertiesLabel::tableName() . ' b', 'a.properties_label_id = b.properties_label_id')
            ->where(['a.properties_id' => $model['properties_id']])
            ->asArray()
            ->all();

        $house = HouseType::find()
            ->select([
                'min(square_metre) as min_square_metre',
                'max(square_metre) as max_square_metre',
            ])
            ->where(['properties_id' => $model['properties_id']])
            ->asArray()
            ->one();

        $model['pic'] = json_decode($model['pic']);
        $model['date'] = date('Y-m-d', $model['date']);
        $model['sale_status'] = Yii::$app->params['sale_status'][$model['sale_status']];
        $model['label'] = $label;

        if ($house)
        {
            $model['min_square_metre'] = $house['min_square_metre'];
            $model['max_square_metre'] = $house['max_square_metre'];
        }

        return response($model);

    }
    /**
     * 预约看房
     * @return array
     */
    public function actionCreate()
    {
        $propertiesId = Yii::$app->request->post('properties_id', 0);
        $userId = Yii::$app->request->post('user_id', 0);
        $houseTypeId = Yii::$app->request->post('house_type_id', 0);
        $timeSlot = Yii::$app->request->post('time_slot', '');
        $date = Yii::$app->request->post('date', '');

        // 是否已经有预约过
        $model = AppointSeeHouse::find()
            ->where([
                'properties_id' => $propertiesId,
                    'user_id' => $userId
            ])
            ->andWhere(['<', 'status', 2])
            ->one();

        if ($model)
        {
            return response([], '20001');
        }

        $model = new  AppointSeeHouse();
        $model->properties_id = $propertiesId;
        $model->house_type_id = $houseTypeId;
        $model->user_id = $userId;
        $model->time_slot = $timeSlot;
        $model->date = strtotime($date);
        $model->create_time = time();

        if (!$model->save())
        {
            return response([], '20001');
        }
        return response();
    }
}
