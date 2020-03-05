<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-02-23 21:00
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseController;
use common\models\Ad;

class AdController extends BaseController
{
    /**
     * 首页广告位
     *
     * @return array
     */
    public function actionIndex()
    {
        $model = Ad::find()
            ->where([
                'status' => '1',
                'type' => '0',
            ])
            ->asArray()
            ->one();
        if ($model)
        {
            if ($model['pic']) {
                $model['pic'] = json_decode($model['pic'] );
            }
        }

        return response($model);
    }
}
