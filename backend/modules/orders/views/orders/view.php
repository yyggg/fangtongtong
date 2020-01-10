<?php
use yii\widgets\DetailView;
use backend\assets\LayuiAsset;
LayuiAsset::register($this);
?>
<div class="user-view">
    <?= DetailView::widget([
        'model' => $model,
		'options' => ['class' => 'layui-table'],
		'template' => '<tr><th width="35%">{label}</th><td>{value}</td></tr>',
        'attributes' => [
            'id',
            'name',
            'phone',
            'goods_name',
            'source',
            'number',
            'order_no',
            'logistic_code',
            'shipper_code',
            'address',
            'price',
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
            'create_time',
            'admin_id',
            'remark',
            'return_remark',
        ],
		'template' => '<tr><th width="35%">{label}</th><td>{value}</td></tr>',
    ]) ?>

</div>
