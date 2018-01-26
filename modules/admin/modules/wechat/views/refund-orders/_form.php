<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wechat\models\RefundOrder */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="order-refund-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'appid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mch_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nonce_str')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sign')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sign_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'transaction_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'out_trade_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'out_refund_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'total_fee')->textInput() ?>

    <?= $form->field($model, 'refund_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'refund_fee')->textInput() ?>

    <?= $form->field($model, 'refund_fee_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'refund_desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'refund_account')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
