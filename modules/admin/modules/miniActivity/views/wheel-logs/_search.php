<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\WheelLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wheel-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'wheel_id') ?>

    <?= $form->field($model, 'is_win') ?>

    <?= $form->field($model, 'award_id') ?>

    <?= $form->field($model, 'ip_address') ?>

    <?php // echo $form->field($model, 'post_datetime') ?>

    <?php // echo $form->field($model, 'member_id') ?>

    <?php // echo $form->field($model, 'is_get') ?>

    <?php // echo $form->field($model, 'get_password') ?>

    <?php // echo $form->field($model, 'get_datetime') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
