<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wxpay\models\PayOrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="pay-order-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'mch_appid') ?>

    <?= $form->field($model, 'mchid') ?>

    <?= $form->field($model, 'device_info') ?>

    <?= $form->field($model, 'nonce_str') ?>

    <?php // echo $form->field($model, 'sign') ?>

    <?php // echo $form->field($model, 'partner_trade_no') ?>

    <?php // echo $form->field($model, 'payment_no') ?>

    <?php // echo $form->field($model, 'transfer_time') ?>

    <?php // echo $form->field($model, 'payment_time') ?>

    <?php // echo $form->field($model, 'openid') ?>

    <?php // echo $form->field($model, 'check_name') ?>

    <?php // echo $form->field($model, 're_user_name') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'desc') ?>

    <?php // echo $form->field($model, 'spbill_create_ip') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'reason') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
