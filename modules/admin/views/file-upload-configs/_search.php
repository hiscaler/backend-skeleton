<?php

use app\models\FileUploadConfig;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FileUploadConfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column">
    <div class="file-upload-config-search form">
        <?php
        $form = ActiveForm::begin([
            'id' => 'form-upload-configs-search',
            'action' => ['index'],
            'method' => 'get',
        ]);
        ?>
        <div class="entry">
            <?= $form->field($model, 'model_name')->dropDownList(FileUploadConfig::validModelNames(), ['prompt' => '']) ?>

            <?= $form->field($model, 'type')->dropDownList(FileUploadConfig::typeOptions(), ['prompt' => '']) ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
