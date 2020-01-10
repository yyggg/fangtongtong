<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\LayuiAsset;
LayuiAsset::register($this);
$this->registerJs($this->render('js/appoint.js'));

?>
<div class="user-form create_box">
    <?php $form = ActiveForm::begin(['options' => ['class' => 'layui-form form-horizontal'],'fieldConfig' => [
        'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-9'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>",
    ]]); ?>

    <div class="layui-row">
        <div class="layui-col-xs6">
        </div>

    </div>

    <br>
    <?= Html::hiddenInput('ids', '', ['id' => 'order_ids']);?>
    <?= Html::dropDownList('appoint_admin_id', '', \yii\helpers\ArrayHelper::map($users,'admin.id', 'admin.nickname'), ['id' => 'appoint_admin_id'])?>
    <br>
    <div align='right'>
        <?= Html::button( '分配', ['class' => 'layui-btn layui-btn-normal appoint']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
