<?php
namespace backend\controllers;

use common\models\RollInLogs;
use common\models\RollOutLogs;
use common\models\User;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        /*$todayBegin = date('Y-m-d') . ' 00:00:00';
        $userStat['total'] = User::find()->count();
        $userStat['today_reg_total'] = User::find()->where(['>', 'create_time', $todayBegin])->count();
        $amount['roll_in'] = RollInLogs::find()->where(['status' => 2])->andWhere(['>', 'update_time', $todayBegin])->sum('point');
        $amount['roll_out'] = RollOutLogs::find()->where(['status' => 2])->andWhere(['>', 'update_time', $todayBegin])->sum('point');*/

        return $this->render('index',[
            'data'=> [
                //'userStat' => $userStat,
                //'amount'=> $amount,
            ],
        ]);
    }
}
