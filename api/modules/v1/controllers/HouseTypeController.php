<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;


use common\models\HouseType;
use common\models\Properties;
use Yii;
use api\controllers\BaseCotroller;

class HouseTypeController extends BaseCotroller
{
    /**
     * 户型列表
     * @return array
     */
    public function actionIndex()
    {
        $data = ['house_type_list' => [], 'category' => [], 'status_type' => []];
        $category = [];
        $propertiesId = Yii::$app->request->get('properties_id', 0);

        $model =  HouseType::find()
            ->select([
                'house_type_id',
                'name', 'pic',
                'square_metre', 'status',
                'status', 'room_category_id'
            ])
            ->where(['properties_id' => $propertiesId])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['sale_status'] = Yii::$app->params['sale_status'][$v['status']];
            $v['square_metre'] = floatval($v['square_metre']);
            $data['house_type_list'][$v['room_category_id']][] = $v;
            $category[$v['room_category_id']]['name'] = Yii::$app->params['house_type'][$v['room_category_id']];
            !isset($category[$v['room_category_id']]['count']) && $category[$v['room_category_id']]['count'] = 0;
            $category[$v['room_category_id']]['count']++;
            $data['status_type'][$v['status']]['name'] = $v['sale_status'];
            !isset($data['status_type'][$v['status']]['count']) && $data['status_type'][$v['status']]['count'] = 0;
            $data['status_type'][$v['status']]['count']++;
        }
        $data['category'] = $category;
        $data['count'] = count($model);

        return response($data);
    }

    /**
     * 获取楼盘户型详情
     * @return array
     */
    public function actionInfo()
    {
        $houseTypeId = Yii::$app->request->get('house_type_id', 0);

        $model = HouseType::find()
            ->where(['house_type_id' => $houseTypeId])
            ->asArray()
            ->one();

        if ($model)
        {
            $model['square_metre'] = floatval($model['square_metre']);
            $model['sale_status'] = Yii::$app->params['sale_status'][$model['status']];

            $properties = Properties::find()->where(['properties_id' => $model['properties_id']])->asArray()->one();
            $model['properties_name'] = $properties['name'];
            $model['property_type'] = $properties['property_type_id'];
        }

        return response($model);
    }
}
