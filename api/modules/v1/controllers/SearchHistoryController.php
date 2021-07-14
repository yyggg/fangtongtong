<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-05 09:47
 */

namespace api\modules\v1\controllers;

use Yii;
use common\models\SearchHistory;
use api\controllers\BaseController;

class SearchHistoryController extends BaseController
{
    /**
     * 搜索历史列表
     * @return array
     */
    public function actionIndex()
    {
        $type = Yii::$app->request->get('type', 0);
        $userId = Yii::$app->request->get('user_id', 0);

        $model = SearchHistory::find()
            ->select(['keyword'])
            ->where(['user_id' => $userId, 'type' => $type])
            ->asArray()
            ->all();

        return response($model);
    }
}
