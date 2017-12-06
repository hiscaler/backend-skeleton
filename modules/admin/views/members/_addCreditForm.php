<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserCreditLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-outside">
    <div class="form user-credit-log-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'credits')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'remark')->textarea() ?>

        <div class="form-group buttons">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
