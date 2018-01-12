<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserGroup */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="user-group-form form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'type')->dropDownList(app\models\UserGroup::typeOptions()) ?>

        <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'icon_path')->fileInput() ?>

        <?= $form->field($model, 'min_credits')->textInput() ?>

        <?= $form->field($model, 'max_credits')->textInput() ?>
        <div class="form-group buttons">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
