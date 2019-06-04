<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\exam\models\Question */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="question-form form">
        <?php
        $form = ActiveForm::begin([
            'enableAjaxValidation' => true,
        ]);
        ?>

        <?= $form->field($model, 'question_bank_id')->dropDownList(\app\models\QuestionBank::getItems()) ?>

        <?= $form->field($model, 'type')->dropDownList(\app\models\Question::typeOptions()) ?>

        <?= $form->field($model, 'status')->dropDownList(\app\models\Question::statusOptions()) ?>

        <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'options')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'answer')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'resolve')->textarea(['rows' => 6]) ?>
        <div class="form-group buttons">
            <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
