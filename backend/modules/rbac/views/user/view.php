<?php
use yii\widgets\DetailView;
use backend\assets\LayuiAsset;

LayuiAsset::register($this);
?>
<div class="user-view">
    <?=DetailView::widget([
        'model' => $model,
		'options' => ['class' => 'layui-table'],
        'attributes' => [
            'username',
			'nickname',
            [
                "attribute"=>"head_pic",
                "format"=>[
                    "image",
                    [
                        "width"=>"100px",
                        "height"=>"100px",
						"class" => "layui-circle"
                    ],
                ],
            ],
            'email:email',
            'create_time',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return $model->status == 10 ? '启用' : '禁用';
                }
            ],
        ],
		'template' => '<tr><th width="90px;">{label}</th><td>{value}</td></tr>', 
    ])
    ?>

</div>
