<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\LayuiAsset;
LayuiAsset::register($this);
$this->registerJs($this->render('js/create.js'));

?>
<div class="user-form create_box">
    <?php $form = ActiveForm::begin(['options' => ['class' => 'layui-form form-horizontal'],'fieldConfig' => [
        'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-9'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>",
    ]]); ?>

    <div class="layui-row">
        <div class="layui-col-xs6">
            <div class="grid-demo grid-demo-bg1">商品名称：<?= $model->goods_name; ?></div>
        </div>

    </div>

    <br>

    <?= $form->field($model, 'return_remark')->textarea(['class' => 'layui-textarea']) ?>

    <div align='right'>
        <?= Html::submitButton('退回', ['class' => $model->isNewRecord ? 'layui-btn' : 'layui-btn layui-btn-normal']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
