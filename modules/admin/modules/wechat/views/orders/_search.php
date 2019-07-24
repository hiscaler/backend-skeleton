<?php

use app\modules\admin\modules\wechat\models\Order;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wechat\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column">
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
            <?= $form->field($model, 'transaction_id') ?>

            <?= $form->field($model, 'out_trade_no') ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'status')->dropDownList(Order::statusOptions(), ['prompt' => '']) ?>

            <?= $form->field($model, 'trade_state')->dropDownList(Order::tradeStateOptions(), ['prompt' => '']) ?>
        </div>
        <div class="entry">
            <?= \yadjet\datePicker\my97\DatePicker::widget([
                'form' => $form,
                'model' => $model,
                'attribute' => 'begin_date',
                'pickerType' => 'date',
            ]) ?>
            <?= \yadjet\datePicker\my97\DatePicker::widget([
                'form' => $form,
                'model' => $model,
                'attribute' => 'end_date',
                'pickerType' => 'date',
            ]) ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
