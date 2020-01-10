<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;

use common\models\AppointSeeHouse;
use Yii;
use api\controllers\BaseCotroller;

class SeeHouseController extends BaseCotroller
{
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
