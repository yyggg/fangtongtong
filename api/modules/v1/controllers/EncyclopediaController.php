<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;

use common\models\Encyclopedia;
use common\models\EncyclopediaCategory;
use Yii;
use api\controllers\BaseController;

class EncyclopediaController extends BaseController
{
    /**
     * 购买百科分类
     * @return array
     */
    public function actionCategory()
    {
        $model = EncyclopediaCategory::find()->asArray()->all();
        return response($model);
    }

    /**
     * 购房百科列表
     * @return array
     */
    public function actionIndex()
    {
        $cid = Yii::$app->request->get('encyclopedia_category_id', 0);

        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = Encyclopedia::find()
            ->select(['title', 'create_time', 'pic', 'encyclopedia_id'])
            ->where(['encyclopedia_category_id' => $cid])
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['create_time'] = date('Y.m.d H:i:s');
            $model[$k] = $v;
        }

        return response($model);
    }


    /**
     * 购房百科详情
     * @return array
     */
    public function actionInfo()
    {
        $encyclopediaId = Yii::$app->request->get('encyclopedia_id', 0);

        $model = Encyclopedia::find()
            ->select(['title', 'create_time','content'])
            ->where(['encyclopedia_id' => $encyclopediaId])
            ->asArray()
            ->one();

        if ($model)
        {
            $model['create_time'] = date('Y.m.d H:i:s');
        }

        return response($model);
    }


}
