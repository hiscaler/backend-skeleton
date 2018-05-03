<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\signin\models\Signin */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="signin-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'member_id')->textInput() ?>

    <?= $form->field($model, 'ymd')->textInput() ?>

    <?= $form->field($model, 'signin_datetime')->textInput() ?>

    <?= $form->field($model, 'ip_address')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
