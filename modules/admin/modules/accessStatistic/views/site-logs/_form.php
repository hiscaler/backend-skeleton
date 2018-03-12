<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="access-statistic-site-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'site_id')->textInput() ?>

    <?= $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'referrer')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'access_datetime')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
