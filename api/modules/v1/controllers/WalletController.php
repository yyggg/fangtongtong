<?php
/**
 * Created by 260101081@qq.com
 * DateTime 2020-01-26 19:37
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseController;
use common\models\Wallet;
use common\models\WalletLogs;
use Yii;

class WalletController extends BaseController
{
    /**
     * 我的钱包
     * @return array
     */
    public function actionIndex()
    {
        $model = Wallet::find()
            ->where(['user_id' => $this->_userId])
            ->asArray()
            ->one();

        if (!$model)
        {
            $model['account'] = 0;
            $model['points'] = 0;
        }

        return response($model);
    }

    /**
     * 明细
     * @return array
     */
    public function actionLogs()
    {
        $page = Yii::$app->request->get('page', 1);
        $offset = ($page - 1) * Yii::$app->params['pageSize'];

        $model = WalletLogs::find()
            ->where(['user_id' => $this->_userId])
            ->orderBy('wallet_logs_id desc')
            ->offset($offset)
            ->limit(Yii::$app->params['pageSize'])
            ->asArray()
            ->all();

        foreach ($model as $k => $v)
        {
            $v['create_time'] = date('Y.m.d H:i');
            $model[$k] = $v;
        }

        return response($model);
    }

    /**
     * 提现
     * @return array
     */
    public function actionWithdraw()
    {
        $money = Yii::$app->request->post('money', 0);

        $userWallet = Wallet::find()
            ->where(['user_id' => $this->_userId])
            ->asArray()
            ->one();

        if ($money < 1)
        {
            return response([], '30030', '不得少于1元');
        }

        if (!$userWallet || $userWallet['account'] < $money)
        {
            return response([], '30030', '余额不足。');
        }

        $logModel = new WalletLogs();
        $logModel->user_id = $this->_userId;
        $logModel->type = 1;
        $logModel->number = $money;
        $logModel->remark =  Yii::$app->params['wallet_remark'][3];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 插入记录
            $logModel->save();
            // 更新用户钱包
            $walletModel =  Wallet::findOne(['user_id' => $this->_userId]);
            $walletModel->account -= $money;
            $walletModel->save();
            $transaction->commit();
            return response();

        } catch (\Exception $e) {

            $transaction->rollBack();
            return response([], '20001');
        }
    }
}
