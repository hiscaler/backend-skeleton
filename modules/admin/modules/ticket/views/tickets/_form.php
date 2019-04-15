<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\ticket\models\Ticket */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'category_id')->dropDownList(\app\models\Category::tree('ticket.module.category')) ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'confidential_information')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'status')->dropDownList(\app\modules\admin\modules\ticket\models\Ticket::statusOptions()) ?>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
