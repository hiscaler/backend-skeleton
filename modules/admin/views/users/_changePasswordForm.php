<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="form user-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($user, 'username')->textInput(['maxlength' => true, 'disabled' => 'disabled']) ?>

        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'confirmPassword')->passwordInput(['maxlength' => true]) ?>
        <div class="form-group buttons">
            <?= Html::submitButton(Yii::t('app', 'Change Password'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
