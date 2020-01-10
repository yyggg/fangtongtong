<?php

namespace carousel\controllers;

use common\models\Carousel;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Class CarouselController
 * @package carousel\controllers
 */
class CarouselController extends Controller
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
        $query = Carousel::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }


    /**
     * 查看
     *
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        $model = Carousel::find()->where(['carousel_id' => $id])->one();
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * 创建
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Carousel();
        $params = Yii::$app->request->post();

        if ($model->load($params) && $model->validate()) {
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->carousel_id]);
            }
        }
        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * 更新
     *
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $params = Yii::$app->request->post();

        if ($model->load($params)) {

            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->carousel_id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
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
            $model = new Carousel();
            $count = $model->deleteAll(["in","carousel_id",$data['keys']]);
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
        if (($model = Carousel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('请求资源不存在.');
        }
    }



}
