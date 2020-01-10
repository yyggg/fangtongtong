<?php

namespace goods\controllers;

use common\models\Config;
use common\models\Goods;
use common\models\GoodsCategory;
use Yii;
use common\models\searchs\GoodsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class GoodsController extends Controller
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
        $searchModel = new GoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $category = GoodsCategory::find()->indexBy('goods_category_id')->asArray()->all();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $category,
        ]);
    }


    public function actionView($id)
    {
        $article = Goods::find()->where(['goods_id' => $id])->one();
        return $this->render('view', [
            'model' => $article,
        ]);
    }

    public function actionCreate()
    {
        $model = new Goods();
        $post_data = Yii::$app->request->post();

        $category = GoodsCategory::find()->indexBy('goods_category_id')->asArray()->all();

        if ($model->load($post_data) && $model->validate()) {
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->goods_id]);
            }
        }
        //var_dump($model->getErrors());die;
        return $this->render('create', [
            'model' => $model,
            'category' => $category,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $category = GoodsCategory::find()->indexBy('goods_category_id')->asArray()->all();
        $postData = Yii::$app->request->post();

        if ($model->load($postData)) {

            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->goods_id]);
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
            $model = new Goods();
            $count = $model->deleteAll(["in","goods_id",$data['keys']]);
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
        if (($model = Goods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('请求资源不存在.');
        }
    }



    /**
     * Json方式 查询订单物流轨迹
     */
    function getOrderTracesByJson(){
        $requestData= "{'OrderCode':'','ShipperCode':'YTO','LogisticCode':'12345678'}";

        $datas = array(
            'EBusinessID' => EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = encrypt($requestData, AppKey);
        $result=sendPost(ReqURL, $datas);

        //根据公司业务处理返回的信息......

        return $result;
    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }
}
