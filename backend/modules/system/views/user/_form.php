<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$bankTypes = [
    '招商银行' => '招商银行',
    '工商银行' => '工商银行',
    '建设银行' => '建设银行',
    '农业银行' => '农业银行',
    '交通银行' => '交通银行',
    '中国银行' => '中国银行',
    '浦发银行' => '浦发银行',
    '平安银行' => '平安银行',
    '民生银行' => '民生银行',
    '光大银行' => '光大银行',
    '兴业银行' => '兴业银行',
    '邮政银行' => '邮政银行',
    '发展银行' => '发展银行',
    '汉口银行' => '汉口银行',
    '华夏银行' => '华夏银行',
    '北京银行' => '北京银行',
    '上海银行' => '上海银行',
    '浙商银行' => '浙商银行',
];

$this->registerJs($this->render('js/upload.js'));
?>

<div class="user-form create_box">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'layui-form']]); ?>
	
    <?= $form->field($model, 'real_name')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>
	
	<?= $form->field($model, 'bank_type')->dropDownList($bankTypes) ?>

    <?= $form->field($model, 'bank_branch')->textInput(['maxlength' => true,'class'=>'layui-input'])?>
	
	<?= $form->field($model, 'bank_number')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>

	<?= $form->field($model, 'bank_remark_phone')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>

	<?//= $form->field($model, 'alipay')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>

	<?//= $form->field($model, 'tenpay')->textInput(['maxlength' => true,'class'=>'layui-input']) ?>

    <?= $form->field($model, 'password_hash')->passwordInput(['maxlength' => true,'placeholder' =>'留空不修改','value'=>'','class'=>'layui-input search_input']) ?>

    <?= $form->field($model, 'trans_password')->passwordInput(['maxlength' => true,'placeholder' =>'留空不修改','value'=>'','class'=>'layui-input search_input']) ?>

    <div align='right'>
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'layui-btn' : 'layui-btn layui-btn-normal']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

