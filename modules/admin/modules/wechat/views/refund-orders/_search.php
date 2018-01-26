<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wechat\models\RefundOrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column" style="display: none">
    <div class="order-refund-search form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>
        <div class="entry">
            <?= $form->field($model, 'order_id') ?>

            <?= $form->field($model, 'appid') ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'mch_id') ?>

            <?= $form->field($model, 'nonce_str') ?>
        </div>
        <?php // echo $form->field($model, 'sign') ?>

        <?php // echo $form->field($model, 'sign_type') ?>

        <?php // echo $form->field($model, 'transaction_id') ?>

        <?php // echo $form->field($model, 'out_trade_no') ?>

        <?php // echo $form->field($model, 'out_refund_no') ?>

        <?php // echo $form->field($model, 'total_fee') ?>

        <?php // echo $form->field($model, 'refund_id') ?>

        <?php // echo $form->field($model, 'refund_fee') ?>

        <?php // echo $form->field($model, 'refund_fee_type') ?>

        <?php // echo $form->field($model, 'refund_desc') ?>

        <?php // echo $form->field($model, 'refund_account') ?>

        <?php // echo $form->field($model, 'created_at') ?>

        <?php // echo $form->field($model, 'created_by') ?>
        <div class="form-group buttons">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
