<?php

use yii\helpers\Html;
use backend\assets\LayuiAsset;
use yii\widgets\ActiveForm;
LayuiAsset::register($this);
$this->registerJs($this->render('js/export.js'));
?>
<blockquote class="layui-elem-quote" style="font-size: 14px;">
    <div class="user-search">

        <?php $form = ActiveForm::begin([
            'action' => ['export'],
            'method' => 'post',
            'options' => ['class' => 'form-inline'],
            'fieldConfig' => [
                'template' => '<div class="layui-inline">{label}：<div class="layui-input-inline">{input}</div></div><span class="help-block" style="display: inline-block;">{hint}</span>',
            ],
        ]); ?>

        <?= $form->field($model, 'goods_name')->textInput(['class'=>'layui-input search_input']) ?>

        <?= $form->field($model, 'phone') ?>

        <?= $form->field($model, 'source') ?>

        <?= $form->field($model, 'order_time')->textInput([
            'class'=>'layui-input', 'id' => 'order_time', 'style' => 'width:310px;', 'readonly'=>'true'
        ])->label('下单时间范围') ?>
        <div class="form-group">
            <?= Html::submitButton('导出', ['class' => 'layui-btn layui-btn-normal']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</blockquote>

