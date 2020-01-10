<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\LayuiAsset;
use yii\grid\GridView;
use common\models\UserRank;
LayuiAsset::register($this); 
$this->registerJs($this->render('js/index.js'));
/* @var $this yii\web\View */
/* @var $searchModel common\models\searchs\User */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<blockquote class="layui-elem-quote" style="font-size: 14px;">
	<?php  echo $this->render('_search', ['model' => $searchModel]); ?>
</blockquote>
<div class="user-index layui-form news_list">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'options' => ['class' => 'grid-view','style'=>'overflow:auto', 'id' => 'grid'],
		'tableOptions'=> ['class'=>'layui-table'],
		'pager' => [
			'options'=>['class'=>'layuipage pull-right'],
				'prevPageLabel' => '上一页',
				'nextPageLabel' => '下一页',
				'firstPageLabel'=>'首页',
				'lastPageLabel'=>'尾页',
				'maxButtonCount'=>5,
        ],
		'columns' => [
			[
				'class' => 'backend\widgets\CheckboxColumn',
				'checkboxOptions' => ['lay-skin'=>'primary','lay-filter'=>'choose'],
				'headerOptions' => ['width'=>'50','style'=> 'text-align: center;'],
				'contentOptions' => ['style'=> 'text-align: center;']
			],
			[
				'attribute' => 'real_name',
				'headerOptions' => ['width'=>'50','style'=> 'text-align: center;'],
				'contentOptions' => ['style'=> 'text-align: center;']
			],
            'username',
            [
                'attribute' => 'amount',
                'value' => function($model){
                    return number_format($model->amount / 100);
                },
            ],
            [
                'attribute' => 'profit',
                'value' => function($model){
                    return number_format($model->profit / 100);
                },
            ],
            //'alipay',
            //'tenpay',
            //'bank_type',
            //'bank_branch',
            //'nickname',
            [
                'attribute' => 'verified',
                'format' => 'html',
                'value' => function($model) {
                    return $model->verified == 0 ? '<font color="red">未认证</font>' : '<font color="green">已认证</font>';
                },
                'contentOptions' => ['style'=> 'text-align: center;'],
                'headerOptions' => [
                    'width' => '40',
                    'style'=> 'text-align: center;'
                ]
            ],
            [
                'attribute' => 'create_time',
				'headerOptions' => [
					'width' => '90'
				]
            ],
//            [
//                'attribute' => 'last_login_date',
//                'value' => function($model){
//                    return date("Y-m-d H:i:s",$model->last_login_date);
//                },
//				'headerOptions' => [
//					'width' => '8%'
//				]
//            ],
//			'last_login_ip',
//			'last_login_address',
            [
                'attribute' => 'status',
				'format' => 'html',
                'value' => function($model) {
                    return $model->status == 0 ? '<font color="red">禁用</font>' : '<font color="green">启用</font>';
                },
				'contentOptions' => ['style'=> 'text-align: center;'],
				'headerOptions' => [
					'width' => '40',
					'style'=> 'text-align: center;'
				]
            ],
//            [
//                'attribute' => 'updated_at',
//                'value' => function($model){
//                    return date("Y-m-d H:i:s",$model->updated_at);
//                },
//				'headerOptions' => [
//					'width' => '10%'
//				]
//            ],

            [
				'header' => '操作',
				'class' => 'yii\grid\ActionColumn',
				'contentOptions' => ['class'=>'text-center'],
				'headerOptions' => [
					'width' => '10%',
					'style'=> 'text-align: center;'
				],
				'template' =>'{view} {update} {activate} {verified}',
				'buttons' => [
                    'view' => function ($url, $model, $key){
						return Html::a('查看', Url::to(['view','id'=>$model->id]), ['class' => "layui-btn layui-btn-xs layui-default-view"]);
                    },
                    'update' => function ($url, $model, $key) {
						return Html::a('修改', Url::to(['update','id'=>$model->id]), ['class' => "layui-btn layui-btn-normal layui-btn-xs layui-default-update"]);
                    },
                    'activate' => function ($url, $model, $key) {
						if($model->status==0){
							return Html::a('启用', Url::to(['active','id'=>$model->id]), ['class' => "layui-btn layui-btn-xs layui-btn-normal layui-default-active"]);
						}else{
							return Html::a('禁用', Url::to(['inactive','id'=>$model->id]), ['class' => "layui-btn layui-btn-xs layui-btn-warm layui-default-inactive"]);
						}
                    },
                    'verified' => function ($url, $model, $key) {
                        if($model->verified){
                            return '';
                        }
                        else{
                            return Html::a('审核', Url::to(['verified','id'=>$model->id]), ['class' => "layui-btn layui-btn-normal layui-btn-xs layui-default-verified"]);
                        }
                    },
				]
			],
        ],
    ]); ?>
</div>
