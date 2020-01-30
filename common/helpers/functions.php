<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-02 15:05
 */

if (! function_exists('response'))
{
    function response($data = [], $code = '0', $msg = '')
    {

        if (!$data && $code != '0')
        {
            return [
                'errCode' => Yii::$app->params['errCode'][$code]['errCode'],
                'errMsg' => $msg ? $msg : Yii::$app->params['errCode'][$code]['errMsg']];
        }

        return [
            'errCode' => Yii::$app->params['errCode'][$code]['errCode'],
            'data' => $data
        ];

    }

}

if (! function_exists('sendSms'))
{
    function sendSms($phone = [], $content = '', $type = 0)
    {
        $userId = 101;
        $account = '13570919386';
        $password = 'yz13570919386';
        $redisKey = 'sms:' . $phone . '_' . $type;
        $redisIncrKey = 'sms:incr' . $phone . '_' .$type;

        // 每种类型短信一天只能发5条
        if (Yii::$app->redis->get($redisIncrKey) >= 5)
        {
            return false;
        }

        $code = '';
        $pattern='1234567890';
        for( $i=0; $i<6; $i++ ) {
            $code .= $pattern[mt_rand(0, 9)];

        }

        switch ($type) {
            case 0:
                $content = '【房通通】您的注册验证码 '. $code . $content . ' 。';
                break;
            case 1:
                $content = '【房通通】您的登录验证码 '. $code . $content . ' 。';
                break;
            case 2:
                $content = '【房通通】您本次的操作验证码 '. $code . $content . ' 。';
                break;
            default:
                $content = '【房通通】'. $content . ' 。';
        }


        $url = 'http://47.104.171.59:8088/sms.aspx';
        $curl = new linslin\yii2\curl\Curl();

        $response = $curl->setOption(
            CURLOPT_POSTFIELDS,
            http_build_query(array(
                    'action' => 'send',
                    'userid' => $userId,
                    'account' => $account,
                    'password' => $password,
                    'mobile' => $phone,
                    'content' => $content,
                )
            ))
            ->post($url);

        if ($curl->errorCode === null)
        {
           $arr = xmlToArr($response);
           if ($arr['returnstatus'] === 'Success')
           {
               return true;
           }


           Yii::$app->redis->set($redisKey);
           Yii::$app->redis->expire($redisKey, 60);

           Yii::$app->redis->incr($redisIncrKey, 1);
            Yii::$app->redis->expire($redisKey, strtotime('23:59:59')-time());
        }

        return false;
    }

}

if (! function_exists('xmlToArr')) {
    function xmlToArr($path)
    {
        $xml = $path;//XML文件
        $objectxml = simplexml_load_string($xml);//将文件转换成 对象
        $xmljson = json_encode($objectxml);//将对象转换个JSON
        $xmlarray = json_decode($xmljson, true);//将json转换成数组
        return $xmlarray;
    }
}


if (!function_exists('uploads')) {
    function uploads($filename)
    {
        $image = \yii\web\UploadedFile::getInstanceByName($filename);
        $date = date('Ymd');
        $ext = $image->getExtension();
        $path = Yii::getAlias("@uploads") . '/' . $date;
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $filePath = '/' .uniqid().time().$ext;
        $fullName = $path . $filePath;
        if($image->saveAs($fullName)) {
            return  '/' . $date . '/' . $filePath;
        } else {
            return false;
        }
    }
}

if (!function_exists(''))
{

}


