<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\signin\models\SigninSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="signin-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'member_id') ?>

    <?= $form->field($model, 'ymd') ?>

    <?= $form->field($model, 'signin_datetime') ?>

    <?= $form->field($model, 'ip_address') ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
