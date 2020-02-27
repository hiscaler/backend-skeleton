<?php

use app\models\FileUploadConfig;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FileUploadConfig */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="file-upload-config-form form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'type')->dropDownList(FileUploadConfig::typeOptions()) ?>

        <?= $form->field($model, 'model_attribute')->dropDownList(FileUploadConfig::modelAttributeOptions(), ['prompt' => '']) ?>

        <?= $form->field($model, 'extensions')->textInput(['maxlength' => true, 'placeholder' => '*']) ?>

        <?=
        $form->field($model, 'min_size', [
            'template' => "{label}\n{input} KB\n{hint}\n{error}",
        ])->textInput(['class' => 'g-text-number', 'type' => 'number'])
        ?>

        <?=
        $form->field($model, 'max_size', [
            'template' => "{label}\n{input} KB\n{hint}\n{error}",
        ])->textInput(['class' => 'g-text-number', 'type' => 'number'])
        ?>

        <?=
        $form->field($model, 'thumb_width', [
            'template' => "{label}\n{input} px\n{hint}\n{error}",
            'options' => [
                'class' => 'form-group',
                'style' => $model->type == FileUploadConfig::TYPE_IMAGE ? '' : 'display: none;',
            ]
        ])->textInput(['class' => 'g-text-number', 'type' => 'number'])
        ?>

        <?=
        $form->field($model, 'thumb_height', [
            'template' => "{label}\n{input} px\n{hint}\n{error}",
            'options' => [
                'class' => 'form-group',
                'style' => $model->type == FileUploadConfig::TYPE_IMAGE ? '' : 'display: none;',
            ]
        ])->textInput(['class' => 'g-text-number', 'type' => 'number'])
        ?>
        <div class="form-group buttons">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php \app\modules\admin\components\JsBlock::begin() ?>
<script type="text/javascript">
    $(function() {
        $('#fileuploadconfig-type').change(function() {
            var type = $(this).val(),
                fileType = <?= FileUploadConfig::TYPE_FILE ?>,
                imageType = <?= FileUploadConfig::TYPE_IMAGE ?>;
            if (type == fileType) {
                $('#fileuploadconfig-thumb_width, #fileuploadconfig-thumb_height').parent().hide();
            } else {
                $('#fileuploadconfig-thumb_width, #fileuploadconfig-thumb_height').parent().show();
            }
            if (!$('#fileuploadconfig-extensions').val()) {
                if (type == fileType) {
                    $('#fileuploadconfig-extensions').val('zip,doc,docx,xls,xlsx,pdf');
                } else if (type == imageType) {
                    $('#fileuploadconfig-extensions').val('jpg,jpeg,png,gif');
                }
            }
        });
    });
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
