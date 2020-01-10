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
            'title',
            'content:html',
            'create_time:datetime',
        ],
		'template' => '<tr><th width="35%">{label}</th><td>{value}</td></tr>',
    ]) ?>

</div>
