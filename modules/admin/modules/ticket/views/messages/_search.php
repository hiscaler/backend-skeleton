<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\ticket\models\TicketMessageSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column">
    <div class="form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>
        <div class="entry">
            <?= $form->field($model, 'ticket_id', ['template' => '{input}'])->hiddenInput(['value' => $ticketId]) ?>
            <?= $form->field($model, 'type')->dropDownList(\app\modules\admin\modules\ticket\models\TicketMessage::typeOptions(), ['prompt' => '']) ?>

            <?= $form->field($model, 'reply_username') ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
