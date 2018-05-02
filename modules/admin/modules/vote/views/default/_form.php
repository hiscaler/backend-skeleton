<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\vote\models\Vote */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-layout-column">
    <div class="vote-form form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->errorSummary($model) ?>
        <div class="entry">
            <?= $form->field($model, 'category_id')->dropDownList(\app\models\Category::tree('vote.module.category'), ['prompt' => '']) ?>

            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        <div class="entry">
            <?= \yadjet\datePicker\my97\DatePicker::widget(['form' => $form,
                'model' => $model,
                'attribute' => 'begin_datetime',
                'pickerType' => 'datetime',])
            ?>

            <?= \yadjet\datePicker\my97\DatePicker::widget(['form' => $form,
                'model' => $model,
                'attribute' => 'end_datetime',
                'pickerType' => 'datetime',])
            ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'allow_anonymous')->checkbox() ?>

            <?= $form->field($model, 'allow_view_results')->checkbox() ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'allow_multiple_choice')->checkbox() ?>

            <?= $form->field($model, 'interval_seconds')->textInput() ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'ordering')->dropDownList(\app\models\Option::ordering()) ?>

            <?= $form->field($model, 'enabled')->checkbox() ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
