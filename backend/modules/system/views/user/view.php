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
            'username',
            'real_name',
            [
                'attribute' => 'amount',
                'value' => function($model){
                    return $model['amount'] / 100;
                }
            ],
            [
                'attribute' => 'profit',
                'value' => function($model){
                    return $model['profit'] / 100;
                }
            ],
            'bank_type',
            'bank_branch',
            'bank_number',
            'bank_remark_phone',
            [
                'attribute' => '推荐人',
                'value' => function($model){
                    return isset($model['referee']) ? $model['referee'] : '';
                }
            ],
            [
                'attribute' => 'verified',
                'value' => function($model){
                    return $model['status'] == 0 ? '未认证' : '已认证';
                }
            ],
            [
				'attribute' => 'status',
				'value' => function($model){
					return $model['status'] == 0 ? '禁用' : '启用';
				}
			],
            'create_time',
            'update_time',
        ],
		'template' => '<tr><th width="35%">{label}</th><td>{value}</td></tr>',
    ]) ?>

</div>
