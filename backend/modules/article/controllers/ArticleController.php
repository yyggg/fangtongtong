<?php

namespace article\controllers;

use common\models\Article;
use common\models\ArtCategory;
use common\models\Config;
use Yii;
use common\models\searchs\ArticleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class ArticleController extends Controller
{
    /**
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

    public function actions()
    {
        $cfg = Config::find()->where(['name' => 'WEB_SITE_RESOURCES_URL'])->one();
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix" => $cfg->value,//图片访问路径前缀
                    "imagePathFormat" => "{yyyy}{mm}{dd}/{time}{rand:6}", //上传保存路径
                    "imageRoot" => Yii::getAlias("@uploads"),
                ],
            ]
        ];
    }
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $category = ArtCategory::find()->indexBy('art_category_id')->asArray()->all();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $category,
        ]);
    }


    public function actionView($id)
    {
        $article = Article::find()->where(['article_id' => $id])->one();
        return $this->render('view', [
            'model' => $article,
        ]);
    }

    public function actionCreate()
    {
        $model = new Article();
        $post_data = Yii::$app->request->post();

        $category = ArtCategory::find()->indexBy('art_category_id')->asArray()->all();

        if ($model->load($post_data) && $model->validate()) {
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->article_id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
            'category' => $category,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $category = ArtCategory::find()->indexBy('art_category_id')->asArray()->all();

        $postData = Yii::$app->request->post();

        if ($model->load($postData)) {

            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->article_id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'category' => $category,
            ]);
        }
    }

    /**
     * 删除
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

    public function actionDeleteAll(){
        $data = Yii::$app->request->post();
        if($data){
            $model = new Article();
            $count = $model->deleteAll(["in","article_id",$data['keys']]);
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


    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('请求资源不存在.');
        }
    }
}
