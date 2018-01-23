<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wxpay\models\PayOrder */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="pay-order-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'mch_appid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mchid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_info')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nonce_str')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sign')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'partner_trade_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'payment_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'transfer_time')->textInput() ?>

    <?= $form->field($model, 'payment_time')->textInput() ?>

    <?= $form->field($model, 'openid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'check_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 're_user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'spbill_create_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
