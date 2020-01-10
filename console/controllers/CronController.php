<?php
/**
 * Created by 老杨.
 * User: 260101081@qq.com
 * Date: 2018/10/12 22:04
 */
namespace console\controllers;

use Yii;
use common\models\Orders;

class CronController extends \yii\console\Controller
{
    public  $apiUrl = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx'; //快递鸟API请求地址
    private $appKey = '810ffc78-fa45-4fc7-b602-c5b737d61d67'; //快递鸟KEY
    private $appID = '1449565'; //快递鸟ID

    /**
     * 定时任务更新物流状态
     */
    public function actionWuliu()
    {
        $order = Orders::find()->select(['logistic_code','shipper_name','id', 'shipper_code', 'logistic_status'])
            ->where(['not', 'logistic_code' => ''])
            ->andWhere(['in', 'logistic_status', [0, 2]])->all();
        /**
         * OrderCode 订单编号
         * ShipperCode 快递公司编码
         * LogisticCode 物流运单号
         */
        foreach ($order as $v){
            $requestData = "{'OrderCode':'','ShipperCode':'" . $v->shipper_code . "','LogisticCode':'" . $v->logistic_code . "'}";

            $datas = array(
                'EBusinessID' => $this->appID,
                'RequestType' => '1002',
                'RequestData' => urlencode($requestData) ,
                'DataType' => '2',
            );
            $datas['DataSign'] = $this->encrypt($requestData, $this->appKey);
            $result = $this->sendPost($this->apiUrl, $datas);
            $result = json_decode($result, true);
            if($result['State'] != $v->logistic_status)
            {
                $orderModel = Orders::findOne($v->id);
                $orderModel->logistic_status = $result['State'];
                $orderModel->save(false);
            }
        }

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