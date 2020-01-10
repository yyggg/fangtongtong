<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2019-12-31 19:40
 */
namespace api\modules\v1\controllers;

use api\controllers\BaseCotroller;
use common\models\ServeScope;


class ServeScopeController extends BaseCotroller
{

    /**
     * 获取服务范围列表
     * @return array
     */
    public function actionIndex()
    {
        $model = ServeScope::find()
            ->asArray()
            ->all();
        return response($model);
    }

}
