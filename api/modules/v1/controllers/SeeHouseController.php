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
                'a.idcard',
                'a.phone',
                'a.name as username',
                'a.remark',
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

    /**
     * 预约详情
     * @return array
     */
    public function actionInfo()
    {
        $id = Yii::$app->request->get('appoint_see_room_id', 0);

        $model = AppointSeeHouse::find()
            ->alias('a')
            ->select([
                'a.status',
                'a.date',
                'a.idcard',
                'a.phone',
                'a.address',
                'a.name as username',
                'a.remark',
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
     * 楼盘预约详情
     * @return array
     */
    public function actionInfoByProperties()
    {
        $propertiesId = Yii::$app->request->get('properties_id', 0);
        $model = AppointSeeHouse::find()
            ->select([
                'appoint_see_room_id','properties_id','idcard','address','date','status','phone','name','remark'
            ])
            ->where([
                'properties_id' => $propertiesId,
                'user_id' => $this->_userId,
            ])
            ->asArray()
            ->one();
        if ($model)
        {
            $model['date'] = date('Y-m-d', $model['date']);
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
        $userId = $this->_userId;
        $houseTypeId = Yii::$app->request->post('house_type_id', 0);
        $idcard = Yii::$app->request->post('idcard', '');
        $idcard = substr($idcard, -6);
        $phone = Yii::$app->request->post('phone', '');
        $name = Yii::$app->request->post('name', '');
        $remark = Yii::$app->request->post('remark', '');
        $date = Yii::$app->request->post('date', '');
        $address = Yii::$app->request->post('address', '');

        // 是否已经有预约过
        $model = AppointSeeHouse::find()
            ->where([
                'properties_id' => $propertiesId,
                'user_id' => $userId,
                'phone' => $phone
            ])
            ->andWhere(['<', 'status', 2])
            ->one();

        if ($model)
        {
            if ($model->status < 1)
            {
                // 可编辑
                $model->name = $name;
                $model->idcard = $idcard;
                $model->remark = $remark;
                $model->date = strtotime($date);
                if ($model->save())
                {
                    return response();
                }
                else
                {
                    return response([], '20001');
                }
            }
            else
            {
                return response([], '30030', '顾问已接单不能编辑。');
            }
        }

        $model = new  AppointSeeHouse();
        $model->properties_id = $propertiesId;
        $model->house_type_id = $houseTypeId;
        $model->user_id = $userId;
        $model->name = $name;
        $model->phone = $phone;
        $model->idcard = $idcard;
        $model->remark = $remark;
        $model->address = $address;
        $model->date = strtotime($date);
        $model->create_time = time();

        if (!$model->save(false))
        {
            return response([], '20001', '预约提交失败');
        }
        return response();
    }

    /**
     * 取消预约
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionCancel()
    {
        $id = Yii::$app->request->get('appoint_see_room_id', 0);

        $model = AppointSeeHouse::findOne([
            'user_id' => $this->_userId,
            'appoint_see_room_id' => $id,
            'status' => '0',
        ]);

        if ($model->delete())
        {
            return response();
        }
        return response([], '20001');
    }
}
