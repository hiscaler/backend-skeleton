<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\exam\models\QuestionSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="question-search">
    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);
    ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'question_bank_id') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'content') ?>

    <?php // echo $form->field($model, 'options') ?>

    <?php // echo $form->field($model, 'answer') ?>

    <?php // echo $form->field($model, 'resolve')  ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
