<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wechat\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="order-form form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'appid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'mch_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'device_info')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'nonce_str')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sign')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sign_type')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'transaction_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'out_trade_no')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'body')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'detail')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'attach')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fee_type')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'total_fee')->textInput() ?>

        <?= $form->field($model, 'spbill_create_ip')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'time_start')->textInput() ?>

        <?= $form->field($model, 'time_expire')->textInput() ?>

        <?= $form->field($model, 'goods_tag')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'trade_type')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'product_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'limit_pay')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'openid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'status')->textInput() ?>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
