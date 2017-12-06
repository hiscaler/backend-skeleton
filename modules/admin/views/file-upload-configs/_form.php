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

        <?= $form->field($model, 'extensions')->textInput(['maxlength' => 255]) ?>

        <?=
        $form->field($model, 'min_size', [
            'template' => "{label}\n{input} KB\n{hint}\n{error}",
        ])->textInput(['class' => 'g-text-number'])
        ?>

        <?=
        $form->field($model, 'max_size', [
            'template' => "{label}\n{input} KB\n{hint}\n{error}",
        ])->textInput(['class' => 'g-text-number'])
        ?>

        <?=
        $form->field($model, 'thumb_width', [
            'template' => "{label}\n{input} PX\n{hint}\n{error}",
        ])->textInput(['class' => 'g-text-number'])
        ?>

        <?=
        $form->field($model, 'thumb_height', [
            'template' => "{label}\n{input} PX\n{hint}\n{error}",
        ])->textInput(['class' => 'g-text-number'])
        ?>

        <div class="form-group buttons">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
