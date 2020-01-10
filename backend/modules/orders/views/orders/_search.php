<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use rbac\components\Helper;
/* @var $this yii\web\View */
/* @var $model common\models\searchs\User */
/* @var $form yii\widgets\ActiveForm */
AppAsset::register($this);
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
		'fieldConfig' => [
		   'template' => '<div class="layui-inline">{label}：<div class="layui-input-inline">{input}</div></div><span class="help-block" style="display: inline-block;">{hint}</span>',
	   ],
    ]); ?>

    <?= $form->field($model, 'goods_name')->textInput(['class'=>'layui-input search_input']) ?>

    <?= $form->field($model, 'phone') ?>

    <?= $form->field($model, 'source') ?>

    <?= $form->field($model, 'order_status')->dropDownList(['' => '全部'] + Yii::$app->params['order.status']) ?>

    <?= $form->field($model, 'logistic_status')->dropDownList(['' => '全部'] + Yii::$app->params['logistic.status']) ?>


    <?= $form->field($model, 'order_time')->textInput([
            'class'=>'layui-input', 'id' => 'test16', 'style' => 'width:310px;', 'readonly'=>'true'
    ])->label('下单时间范围') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'r_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('查找', ['class' => 'layui-btn layui-btn-normal']) ?>

        <?php if(Helper::checkRoute('delete-all')) :?>
            <?= Html::button('批量删除', ['class' => 'layui-btn layui-btn-danger gridview layui-default-delete-all']) ?>
        <?php endif;?>

        <?php if(Helper::checkRoute('appoint')) :?>
            <?= Html::button('分配', ['class' => 'layui-btn layui-btn-danger gridview layui-default-appoint']) ?>
        <?php endif;?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
