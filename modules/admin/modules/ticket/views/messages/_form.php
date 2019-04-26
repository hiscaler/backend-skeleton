<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\ticket\models\TicketMessage */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'type')->dropDownList(\app\modules\admin\modules\ticket\models\TicketMessage::typeOptions()) ?>

        <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
