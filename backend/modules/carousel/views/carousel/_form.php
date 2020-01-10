<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
$this->registerJs($this->render('js/upload.js'));
?>

<div class="user-update">
    <div class="user-form create_box">

        <?php $form = ActiveForm::begin(['options' => ['class' => 'layui-form form-horizontal'],'fieldConfig' => [
            'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-9'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>",
        ]]); ?>


        <?= $form->field($model, 'title')->textInput(['maxlength' => true,'class'=>'layui-input'])?>


        <?= $form->field($model, 'pic',[
            'template' => '<div class=\'col-xs-3 col-sm-2 text-right\'>{label}</div><div class=\'col-xs-9 col-sm-10\'>{input}<button type="button" class="layui-btn upload_button" id="pic"><i class="layui-icon"></i>上传文件</button>{error}{hint}</div>'
    ])->textInput(['maxlength' => true,'class'=>'layui-input upload_input']) ?>

        <div class="col-xs-3 col-sm-2"></div>
        <div id="pic_view" class="col-xs-9 col-sm-9">
            <?php if($model->pic):?>
                <img src="<?= $model->pic; ?>" width="300">
            <?php endif;?>
        </div>

        <?= $form->field($model, 'info')->textarea(['rows' => 5,'class'=>'layui-textarea']) ?>
        <?= $form->field($model, 'href')->textInput(['maxlength' => true,'class'=>'layui-input'])?>
        <?= $form->field($model, 'sort')->textInput(['maxlength' => true,'class'=>'layui-input'])?>
        <?= $form->field($model, 'status')->dropDownList(Yii::$app->params['status']) ?>


        <div align='right'>
            <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'layui-btn' : 'layui-btn layui-btn-normal']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

