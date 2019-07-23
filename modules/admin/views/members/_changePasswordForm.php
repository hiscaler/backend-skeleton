<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\forms\ChangePasswordForm */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="form user-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($member, 'username')->textInput(['maxlength' => true, 'readonly' => 'readonly', 'disabled' => 'disabled']) ?>

        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'confirm_password')->passwordInput(['maxlength' => true]) ?>
        <div class="form-group buttons">
            <?= Html::submitButton(Yii::t('app', 'Change Password'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
