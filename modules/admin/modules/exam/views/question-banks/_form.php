<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\exam\models\QuestionBank */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="question-bank-form form">
        <?php
        $form = ActiveForm::begin([
            'options' => [
                'enctype' => 'multipart/form-data',
            ],
        ]);
        ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'icon')->fileInput() ?>

        <?= $form->field($model, 'status')->dropDownList(\app\models\QuestionBank::statusOptions()) ?>
        <div class="form-group buttons">
            <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
