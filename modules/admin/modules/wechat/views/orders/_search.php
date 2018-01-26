<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wechat\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column" style="display: none">
    <div class="order-search form">
        <?php $form = ActiveForm::begin([
            'id' => 'form-orders',
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>
        <div class="entry">
            <?= $form->field($model, 'appid') ?>
            <?= $form->field($model, 'mch_id') ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'transaction_id') ?>

            <?= $form->field($model, 'out_trade_no') ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'trade_state')->dropDownList(\app\modules\admin\modules\wechat\models\Order::tradeStateOptions(), ['prompt' => '']) ?>
        </div>
        <?php // $form->field($model, 'device_info') ?>

        <?php // $form->field($model, 'nonce_str') ?>

        <?php // echo $form->field($model, 'sign') ?>

        <?php // echo $form->field($model, 'sign_type') ?>

        <?php // echo $form->field($model, 'transaction_id') ?>

        <?php // echo $form->field($model, 'out_trade_no') ?>

        <?php // echo $form->field($model, 'body') ?>

        <?php // echo $form->field($model, 'detail') ?>

        <?php // echo $form->field($model, 'attach') ?>

        <?php // echo $form->field($model, 'fee_type') ?>

        <?php // echo $form->field($model, 'total_fee') ?>

        <?php // echo $form->field($model, 'spbill_create_ip') ?>

        <?php // echo $form->field($model, 'time_start') ?>

        <?php // echo $form->field($model, 'time_expire') ?>

        <?php // echo $form->field($model, 'goods_tag') ?>

        <?php // echo $form->field($model, 'trade_type') ?>

        <?php // echo $form->field($model, 'product_id') ?>

        <?php // echo $form->field($model, 'limit_pay') ?>

        <?php // echo $form->field($model, 'openid') ?>

        <?php // echo $form->field($model, 'status') ?>
        <div class="form-group buttons">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
