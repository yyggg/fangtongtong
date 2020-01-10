<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
$this->registerJs($this->render('js/upload.js'));
?>

<div class="user-update">
    <div class="user-form create_box">

        <?php $form = ActiveForm::begin(['options' => ['class' => 'layui-form form-horizontal'],'fieldConfig' => [
            'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-9'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-lg-offset-2 col-sm-3 col-sm-offset-0'>{error}</div>",
        ]]); ?>


        <?= $form->field($model, 'title')->textInput(['maxlength' => true,'class'=>'layui-input'])?>


        <?=
        $form->field($model,'content')->widget('kucha\ueditor\UEditor',[
            'clientOptions' => [
                //编辑区域大小
                'initialFrameHeight' => '200',
                //设置语言
                'lang' =>'en', //中文为 zh-cn
                //定制菜单
                'toolbars' => [
                    [
                        'fullscreen', 'source', 'undo', 'redo', '|',
                        'fontsize',
                        'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat',
                        'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|',
                        'forecolor', 'backcolor', '|',
                        'lineheight', '|',
                        'indent', '|'
                    ],
                ]
            ]]);
        ?>


        <div align='right'>
            <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'layui-btn' : 'layui-btn layui-btn-normal']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

