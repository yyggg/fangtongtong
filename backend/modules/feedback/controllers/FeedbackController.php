<?php

namespace feedback\controllers;

use common\models\Feedback;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Class CarouselController
 * @package carousel\controllers
 */
class FeedbackController extends Controller
{
    /**
     * 场景
     *
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 列表
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Feedback::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                //默认排序方式
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                //参与排序的字段
                'attributes' => [
                    'id',  'create_time'
                ],
            ],
            'pagination' => ['pageSize' => 50]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 删除
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
		$model = $this->findModel($id);
		if($model->delete()){
			return json_encode(['code'=>200,"msg"=>"删除成功"]);
		}else{
			$errors = $model->firstErrors;
			return json_encode(['code'=>400,"msg"=>reset($errors)]);
		}
    }

    /**
     * 批量删除
     *
     * @return false|string
     */
    public function actionDeleteAll(){
        $data = Yii::$app->request->post();
        if($data){
            $model = new Feedback();
            $count = $model->deleteAll(["in","id",$data['keys']]);
            if($count>0){
                return json_encode(['code'=>200,"msg"=>"删除成功"]);
            }else{
				$errors = $model->firstErrors;
                return json_encode(['code'=>400,"msg"=>reset($errors)]);
            }
        }else{
            return json_encode(['code'=>400,"msg"=>"请选择数据"]);
        }
    }


    /**
     * 查找一个Model
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Feedback::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('请求资源不存在.');
        }
    }



}
