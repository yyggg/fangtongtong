<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\LayuiAsset;
use yii\grid\GridView;
use rbac\components\Helper;
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
	<div class="layui-btn-group demoTable">
        <?php if(Helper::checkRoute('view')): ?>
		    <a href="<?= Url::to(['create']);?>" class="layui-btn" data-type="getCheckData"><i class="layui-icon">&#xe654;</i>新增订单</a>
        <?php endif;?>

        <?php if(Helper::checkRoute('export')): ?>
		    <button class="layui-btn layui-default-export" data-type="isAll"><i class="layui-icon">&#xe625;</i>导出订单</button>
        <?php endif;?>

        <?php if(Helper::checkRoute('import')): ?>
		    <button class="layui-btn order-import" data-type="getCheckLength"><i class="layui-icon">&#xe67c;</i>导入订单</button>
        <?php endif;?>

        <?php if(Helper::checkRoute('import-logistic-no')): ?>
            <button class="layui-btn order-import-logistic-no" data-type="getCheckLength"><i class="layui-icon">&#xe67c;</i>上传单号</button>
        <?php endif;?>
	</div>

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
				'headerOptions' => ['width'=>'250','style'=> 'text-align: center;'],
				'contentOptions' => ['style'=> 'text-align: center;']
			],
			'order_no',
            'name',
            'phone',
			'address',
            'goods_name',
			'price',
            'source',
            'number',
            'logistic_code',
            'shipper_name',
            [
                'attribute' => 'order_status',
                'value' => function($model){
                    return Yii::$app->params['order.status'][$model['order_status']];
                }
            ],
            [
                'attribute' => 'logistic_status',
                'value' => function($model){
                    return Yii::$app->params['logistic.status'][$model['logistic_status']];
                }
            ],
            [
                'attribute' => 'order_status',
                'label' => '增值单',
                'value' => function($model){
                    return $model['order_status'] == '增值订单' ? '是' : '否';
                }
            ],
            'order_time',
            [
                'attribute' => 'appoint_admin_id',
                'value' => function($model){
                    return $model['appoint_admin_id'] ? $model['nickname'] : '';
                }
            ],
            [
                'attribute' => 'return_remark',
                "format" => 'raw',
                'value' => function($model){
                    return "<span style='color: red'> ". $model['return_remark']. "</span>";
                }
            ],
            [
				'header' => '操作',
				'class' => 'yii\grid\ActionColumn',
				'contentOptions' => ['class'=>'text-center'],
				'headerOptions' => [
					'width' => '10%',
					'style'=> 'text-align: center;'
				],
				'template' =>'{false-order} {confirm-false-order} {return} {wuliu} {view} {update} {delete}',
				'buttons' => [
                    'confirm-false-order' => function ($url, $model, $key){
                        if(Helper::checkRoute('confirm-false-order')){
                            if($model['order_status'] == 2){
                                return Html::a('确认假单', Url::to(['confirm-false-order','id'=>$model['id']]), ['class' => "layui-btn layui-btn-xs layui-btn-normal"]);
                            }
                        }
                    },
                    'return' => function ($url, $model, $key){
                        if(Helper::checkRoute('return')){
                            if(empty($model['return_remark']))
                            {
                                return Html::a('退回', Url::to(['return','id'=>$model['id']]), ['class' => "layui-btn layui-btn-xs layui-btn-normal layui-default-return"]);
                            }
                        }
                    },
                    'wuliu' => function ($url, $model, $key){
                        if(Helper::checkRoute('wuliu')){
                            return Html::a('物流', Url::to(['wuliu','id'=>$model['id']]), ['class' => "layui-btn layui-btn-xs layui-btn-normal layui-default-wuliu"]);
                        }
                    },
                    'view' => function ($url, $model, $key){
                        if(Helper::checkRoute('view')){
                            return Html::a('查看', Url::to(['view','id'=>$model['id']]), ['class' => "layui-btn layui-btn-xs layui-btn-normal layui-default-view"]);
                        }
                    },
                    'update' => function ($url, $model, $key) {
                        if(Helper::checkRoute('update')){
                            return Html::a('修改', Url::to(['update','id'=>$model['id']]), ['class' => "layui-btn layui-btn-warm layui-btn-xs layui-default-update"]);
                        }
                    },
                    'delete' => function ($url, $model, $key) {
                        if(Helper::checkRoute('delete')){
                            return Html::a('删除', Url::to(['delete','id'=>$model['id']]), ['class' => "layui-btn layui-btn-xs layui-btn-danger layui-default-delete"]);
                        }
                    },
				]
			],
        ],
    ]); ?>

</div>
