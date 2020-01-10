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
	<?php  //echo $this->render('_search', ['model' => $dataProvider]); ?>
</blockquote>
<div class="user-index layui-form news_list">
	<div class="layui-btn-group demoTable">
        <?php if(Helper::checkRoute('view')): ?>
		    <a href="#" class="layui-btn layui-default-add" data-type="getCheckData"><i class="layui-icon">&#xe654;</i>新增图片</a>
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
				'headerOptions' => ['width'=>'50','style'=> 'text-align: center;'],
				'contentOptions' => ['style'=> 'text-align: center;']
			],
			'carousel_id',
			'title',
            [
                'attribute' => 'pic',
                'value'=> function($model){
	                return $model['pic'];
                },
                'format'=> ['image', ['width' => 200]]
            ],
            'info',
            'sort',
            'href',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model){
                    return $model['status'] ? Yii::$app->params['status'][$model['status']] : '<span style="color: red;"> '. Yii::$app->params['status'][$model['status']]. '</span>';
                }
            ],
            'create_time:datetime',

            [
				'header' => '操作',
				'class' => 'yii\grid\ActionColumn',
				'contentOptions' => ['class'=>'text-center'],
				'headerOptions' => [
					'width' => '10%',
					'style'=> 'text-align: center;'
				],
				'template' =>'{view} {update} {delete}',
				'buttons' => [
                    'view' => function ($url, $model, $key){
                        if(Helper::checkRoute('view')){
                            return Html::a('查看', Url::to(['view','id'=>$model['carousel_id']]), ['class' => "layui-btn layui-btn-xs layui-btn-normal layui-default-view"]);
                        }
                    },
                    'update' => function ($url, $model, $key) {
                        if(Helper::checkRoute('update')){
                            return Html::a('修改', Url::to(['update','id'=>$model['carousel_id']]), ['class' => "layui-btn layui-btn-warm layui-btn-xs layui-default-update"]);
                        }
                    },
                    'delete' => function ($url, $model, $key) {
                        if(Helper::checkRoute('delete')){
                            return Html::a('删除', Url::to(['delete','id'=>$model['carousel_id']]), ['class' => "layui-btn layui-btn-xs layui-btn-danger layui-default-delete"]);
                        }
                    },
				]
			],
        ],
    ]); ?>

</div>
