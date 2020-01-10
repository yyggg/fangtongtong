<?php

use yii\helpers\Html;
use backend\assets\LayuiAsset;
use yii\widgets\ActiveForm;
LayuiAsset::register($this);
$this->registerJs($this->render('js/import.js'));
?>
<blockquote class="layui-elem-quote" style="font-size: 14px;">
    <div class="user-search">

        <?php $form = ActiveForm::begin([
            'action' => ['import'],
            'method' => 'post',
            //'enctype' => 'multipart/form-data',
            'options' => ['class' => 'form-inline'],
            'fieldConfig' => [
                'template' => '<div class="layui-inline">{label}：<div class="layui-input-inline">{input}</div></div><span class="help-block" style="display: inline-block;">{hint}</span>',
            ],
        ]); ?>

        <div class="layui-upload-drag" id="test10">
            <i class="layui-icon"></i>
            <p>点击上传，或将文件拖拽到此处</p>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</blockquote>

