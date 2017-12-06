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

        <?= $form->field($user, 'username')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'class' => 'g-text']) ?>

        <?= $form->field($model, 'confirmPassword')->passwordInput(['maxlength' => true, 'class' => 'g-text']) ?>

        <div class="form-group buttons">
            <?= Html::submitButton(Yii::t('app', 'Change Password'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
