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
            'carousel_id',
            'title',
            'href',
            [
                'attribute' => 'pic',
                'format' => ['image', ['width' => 300]],
                'value' =>$model->pic
            ],
            'sort',
            'info:html',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return $model['status'] ? '隐藏' : '显示';
                }
            ],
            'create_time:datetime',
        ],
		'template' => '<tr><th width="35%">{label}</th><td>{value}</td></tr>',
    ]) ?>

</div>
