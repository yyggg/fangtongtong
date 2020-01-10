<?php

namespace orders\controllers;

use common\models\Admin;
use common\models\AuthAssignment;
use common\models\Orders;
use Yii;
use common\models\User;
use common\models\searchs\OrdersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class OrdersController extends Controller
{
    public $apiUrl = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx'; //快递鸟API请求地址
    private $appKey = '810ffc78-fa45-4fc7-b602-c5b737d61d67'; //快递鸟KEY
    private $appID = '1449565'; //快递鸟ID
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

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 物流查询
     * @param $id
     * @return string
     */
    public function actionWuliu($id)
    {
        $order = Orders::find()->select(['logistic_code','shipper_name','goods_name', 'shipper_code'])->where(['id' => $id])->one();
        /**
         * OrderCode 订单编号
         * ShipperCode 快递公司编码
         * LogisticCode 物流运单号
         */
        $requestData = "{'OrderCode':'','ShipperCode':'" . $order->shipper_code . "','LogisticCode':'" . $order->logistic_code . "'}";

        $datas = array(
            'EBusinessID' => $this->appID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->appKey);
        $result = $this->sendPost($this->apiUrl, $datas);

        return $this->render('wuliu', [
            'data' => json_decode($result),
            'order' => $order
        ]);
    }

    public function actionView($id)
    {
        $order = Orders::find()->where(['id' => $id])->one();
        $admin = Admin::find()->where(['id' => $order->admin_id])->one();
        $order->admin_id = $admin->nickname;
        return $this->render('view', [
            'model' => $order,
        ]);
    }

    public function actionCreate()
    {        
        $model = new Orders();
		$post_data = Yii::$app->request->post();
        if ($model->load($post_data) && $model->validate()) {
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $postData = Yii::$app->request->post();
        if ($model->load($postData)) {
            if($postData['Orders']['order_status'] == 1)
            {
                $model->order_status = 3;
            }
            $model->admin_id = Yii::$app->user->identity->getId();

            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
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
            $model = new Orders();
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
     * 导出订单
     */
    public function actionExport(){
        $queryParams = Yii::$app->request->queryParams;

        $model = Orders::find()
            ->alias('a')
            ->select('a.id,a.source,a.order_no,a.goods_name,a.number,a.price,a.name,a.phone,a.address,a.remark,
             a.order_time,a.order_status,a.logistic_status,b.nickname,a.logistic_code,a.shipper_code,a.shipper_name,a.return_remark')
            ->leftJoin(Admin::tableName() . ' b', 'a.appoint_admin_id = b.id')
            ->andFilterWhere(['like', 'a.goods_name', $queryParams['OrdersSearch']['goods_name']])
            ->andFilterWhere(['a.phone' => $queryParams['OrdersSearch']['phone']])
            ->andFilterWhere(['a.source' => $queryParams['OrdersSearch']['source']])
            ->andFilterWhere(['a.logistic_status' => $queryParams['OrdersSearch']['logistic_status']])
            ->andFilterWhere(['a.order_status' => $queryParams['OrdersSearch']['order_status']]);


        if($queryParams['OrdersSearch']['order_time']){
            $date = explode(' 到 ', $queryParams['OrdersSearch']['order_time']);
            $model->andWhere(['>', 'a.order_time', $date[0]]);
            $model->andWhere(['<', 'a.order_time', $date[1]]);
        }

        $res = $model->orderBy('a.order_time desc')->asArray()->all();

        $cellData['page_1'][0] = ['ID','来源','订单编号','商品名称','数量','订单金额','姓名','手机','地址','留言备注','下单时间',
            '订单状态','物流状态', '操作员','运单号', '物流公司编号','物流公司名称','退回原因'];
        foreach ($res as $v){
            $v['order_status'] = Yii::$app->params['order.status'][$v['order_status']];
            $v['logistic_status'] = Yii::$app->params['logistic.status'][$v['logistic_status']];

            $cellData['page_1'][] = array_values($v);
        }
        //$cellData['page_1'] = $cellData['page_1'] + $res;
        //print_r($cellData);die;
        /*$cellData = [
            'one' => [
                ['部门', '组别', '姓名', '性别'],
                ['一米技术部', 'oms', 'illusion', '男'],
                ['一米技术部', 'oms', 'alex', '男'],
                ['一米技术部', 'pms', 'aaron', '女']
            ],
            'two' => [
                ['类别', '名称', '价格'],
                ['文学类', '读者', '￥5'],
                ['科技类', 'AI之人工智能', '￥100'],
                ['科技类', '物联网起源', '￥500']
            ],
        ];*/

        Yii::$app->excel->write($cellData);
        Yii::$app->excel->download('订单-' . date('Y-m-d H:i:s'), 'xls'); //'department' 自定义电子表格名
    }

    /**
     * 导入订单
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionImport(){

        $insertTime = date('Y-m-d H:i:s');
        $adminId = Yii::$app->user->identity->id;
        $fields = [
            'source','order_no','goods_name','number','price', 'name', 'phone', 'address','remark', 'order_time','order_status',
            'logistic_status','appoint_admin_id','logistic_code','shipper_code','shipper_name','return_remark','admin_id','create_time',
            //'department' 工作表(sheet)名  键值：['department', 'group', 'name', 'sex']列对应的名称,名称顺序必须一致
        ];

        $importFile = UploadedFile::getInstanceByName('file');
        $rawDatas    = \Yii::$app->excel->read($importFile->tempName);
        if($rawDatas){
            unset($rawDatas[0]);
        }

        $val = '';
        foreach($rawDatas as  $k => $v) {
            unset($v[0]);

            $v[11]  = (int)array_search($v[11], Yii::$app->params['order.status']);
            $v[12]  = (int)array_search($v[12], Yii::$app->params['logistic.status']);
            //查找用户ID
            $user = Admin::findOne(['nickname' => $v[13]]);
            if($user)
            {
                $v[13] = $user->id;
            }
            else{
                $v[13] = 0;
            }
            $v[] = $adminId;
            $v[] = $insertTime;
            $val .= "(".'"'. implode('","', $v) . '"' . "),";
        }

        $val = rtrim($val, ',');

        $sql = 'INSERT INTO mlc_orders(' . implode(',', $fields) . ') VALUES ' . $val;

        $count = Yii::$app->db->createCommand($sql)->execute();

        return json_encode(['count' => $count]);
    }

    /**
     * 导入运单号
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionImportLogisticNo(){

        $count = 0;
        $adminId = Yii::$app->user->identity->id;


        $importFile = UploadedFile::getInstanceByName('file');
        $rawDatas    = \Yii::$app->excel->read($importFile->tempName);
        if($rawDatas){
            unset($rawDatas[0]);
        }

        foreach($rawDatas as  $k => $v) {
            $orderModel = Orders::findOne($v[0]);
            $orderModel->admin_id = $adminId;
            $orderModel->logistic_code = $v[14];
            $orderModel->shipper_code = $v[15];
            $orderModel->shipper_name = $v[16];
            $orderModel->order_no = $v[2];

            $flag = $orderModel->save(false);
            $flag && $count++;
        }

        return json_encode(['count' => $count]);
    }

    /**
     * 分配订单
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionAppoint()
    {
        $post = Yii::$app->request->post();
        if ($post) {
            $result = Orders::updateAll(
                ['appoint_admin_id' => $post['appoint_admin_id'], 'order_status' => 1],
                ['in', 'id', explode(',', $post['ids'])]
            );

            return json_encode(['flag' => $result]);
        } else {
            $users = AuthAssignment::find()->with('admin')->where(['item_name' => '销售'])->all();

            return $this->render('appoint', [
                'users' => $users,
            ]);
        }
    }

    /**
     * 确认假单
     * @param $id
     * @return false|string
     * @throws NotFoundHttpException
     */
    public function actionConfirmFalseOrder($id){
        $model = $this->findModel($id);
        $model->order_status = 4;
        $result = $model->save(false);

        return $this->actionIndex();
    }

    /**
     * 退回
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionReturn($id){
        $model = $this->findModel($id);
        $postData = Yii::$app->request->post();

        if ($model->load($postData)) {
            $model->order_status = 2;
            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('return', [
                'model' => $model,
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = Orders::findOne($id)) !== null) {
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
