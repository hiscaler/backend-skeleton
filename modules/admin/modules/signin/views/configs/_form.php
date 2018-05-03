<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\signin\models\SigninCreditConfig */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-layout-column">
    <div class="signin-credit-config-form form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'message')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'credits')->textInput() ?>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
