<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2019-12-31 19:40
 */
namespace api\modules\v1\controllers;

use api\controllers\BaseCotroller;
use common\models\Region;
use Yii;


class RegionController extends BaseCotroller
{

    /**
     * 获取区域列表
     * @return array
     */
    public function actionArea()
    {
        $regionCode = Yii::$app->request->get('region_code', '');
        $model = Region::find()
            ->select(['region_name', 'region_code'])
            ->where(['parent_code' => $regionCode])
            ->asArray()
            ->all();

        return response($model);
    }

}
