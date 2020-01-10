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

        <?= $form->field($model, 'goods_category_id')->dropDownList(ArrayHelper::map($category,'goods_category_id', 'category_name'), ['prompt'=>'请选择', 'lay-filter' => 'goods_category']) ?>

        <?= $form->field($model, 'goods_name')->textInput(['maxlength' => true,'class'=>'layui-input'])?>

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
                        'fullscreen', 'source', 'undo', 'redo', 'simpleupload','insertimage', '|',
                        'fontsize',
                        'justifyleft', //居左对齐
                        'justifyright', //居右对齐
                        'justifycenter', //居中对齐
                        'justifyjustify', //两端对齐
                        'backcolor', //背景色
                        'imagecenter', //居中
                        'lineheight', //行间距
                        'fontfamily', //字体
                        'preview', //预览
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

