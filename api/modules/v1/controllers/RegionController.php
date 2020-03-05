<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2019-12-31 19:40
 */
namespace api\modules\v1\controllers;

use api\controllers\BaseController;
use common\models\HotCity;
use common\models\Region;
use common\models\UserHistoryCity;
use Yii;


class RegionController extends BaseController
{

    /**
     * 根据城市名获取城市码
     * @return array
     */
    public function actionCityCodeByName()
    {
        $cityName = Yii::$app->request->get('city_name', '');

        $model = Region::find()
            ->select(['region_code'])
            ->where(['level' => '1', 'region_name' => $cityName])
            ->asArray()
            ->one();

        return response($model);
    }
    /**
     * 获取区域列表
     * @return array
     */
    public function actionArea()
    {
        $regionCode = Yii::$app->request->get('city_code', '');
        $model = Region::find()
            ->select(['region_name', 'region_code'])
            ->where(['parent_code' => $regionCode])
            ->asArray()
            ->all();

        return response($model);
    }

    /**
     * 首页-切换城市接口
     * @return array
     */
    public function actionCityChange()
    {
        $data = [];
        $userId = Yii::$app->request->get('user_id', 0);
        $historyCity = UserHistoryCity::find()
            ->select(['region_code', 'name'])
            ->where(['user_id' => $userId])
            ->asArray()
            ->all();

        $hotCity = HotCity::find()
            ->select(['region_code', 'name'])
            ->asArray()
            ->all();

        $city = [];
        $tmpArr = Region::find()
            ->select(['region_name', 'region_code', 'initial'])
            ->where(['level' => '1'])
            ->orderBy('initial asc')
            ->asArray()
            ->all();

        foreach ($tmpArr as $v)
        {
            $city[$v['initial']][] = $v;
        }

        $data['history_city'] = $historyCity;
        $data['hot_city'] = $hotCity;
        $data['city'] = $city;

        return response($data);
    }

}
