<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\WheelLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wheel-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'wheel_id')->textInput() ?>

    <?= $form->field($model, 'is_win')->textInput() ?>

    <?= $form->field($model, 'award_id')->textInput() ?>

    <?= $form->field($model, 'ip_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'post_datetime')->textInput() ?>

    <?= $form->field($model, 'member_id')->textInput() ?>

    <?= $form->field($model, 'is_get')->textInput() ?>

    <?= $form->field($model, 'get_password')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'get_datetime')->textInput() ?>

    <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
