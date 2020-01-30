<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2019-12-31 19:40
 */
namespace api\modules\v1\controllers;

use common\models\PropertiesLabel;
use Yii;
use api\controllers\BaseController;
use common\models\Region;


class FilterController extends BaseController
{
    /**
     * 获取筛选数据
     * @return array
     */
    public function actionIndex()
    {
        $data = [];
        $regionCode = Yii::$app->request->get('region_code', '');
        $data['region'] = Region::find()
            ->select(['region_name', 'region_code'])
            ->where(['parent_code' => $regionCode])
            ->asArray()
            ->all();

        $data['label'] = PropertiesLabel::find()->indexBy('properties_label_id')->asArray()->all();

        $data['unit_price'] = Yii::$app->params['unit_price'];
        $data['total_price'] = Yii::$app->params['total_price'];
        $data['house_type'] = Yii::$app->params['house_type'];
        $data['property_type_name'] = Yii::$app->params['property_type_name'];
        $data['square_metre'] = Yii::$app->params['square_metre'];
        $data['sale_status'] = Yii::$app->params['sale_status'];
        $data['open_time'] = Yii::$app->params['open_time'];

        return response($data);
    }

    public function actionPrice()
    {

    }

}
