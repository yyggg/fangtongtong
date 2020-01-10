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
			'id',
			'name',
			'tel',
			'fax',
			'email',
			'address',
			'title',
            'content',
            'create_time:datetime',

            [
				'header' => '操作',
				'class' => 'yii\grid\ActionColumn',
				'contentOptions' => ['class'=>'text-center'],
				'headerOptions' => [
					'width' => '10%',
					'style'=> 'text-align: center;'
				],
				'template' =>'{delete}',
				'buttons' => [
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
