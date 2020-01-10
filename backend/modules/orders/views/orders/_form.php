<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->registerJs($this->render('js/upload.js'));
?>

<div class="user-update">
    <div class="user-form create_box">

        <?php $form = ActiveForm::begin(['options' => ['class' => 'layui-form form-horizontal'],'fieldConfig' => [
            'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-9'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>",
        ]]); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>

        <?= $form->field($model, 'phone')->textInput(['maxlength' => true,'class'=>'layui-input'])?>

        <?= $form->field($model, 'goods_name')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>

        <?= $form->field($model, 'source')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>

        <?= $form->field($model, 'number')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>

        <?= $form->field($model, 'order_no')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>
        <?= $form->field($model, 'price')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>
        <?= $form->field($model, 'order_time')->textInput(['maxlength' => true,'class'=>'layui-input', 'id' => 'order-time']) ?>
        <?= $form->field($model, 'address')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>

        <?php if(!$model->isNewRecord): ?>
            <?= $form->field($model, 'order_status')->checkbox(['class'=>'layui-input-block'], false)->label('增值单') ?>
        <?php endif;?>

        <?= $form->field($model, 'remark')->textarea(['rows' => 5,'class'=>'layui-textarea']) ?>

        <div align='right'>
            <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'layui-btn' : 'layui-btn layui-btn-normal']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

