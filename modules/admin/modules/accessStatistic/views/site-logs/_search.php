<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column">
    <div class="access-statistic-site-log-search form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>
        <div class="entry">
            <?= $form->field($model, 'ip_repeat_times') ?>

            <?= $form->field($model, 'referrer') ?>
        </div>
        <div class="entry">
            <?= \yadjet\datePicker\my97\DatePicker::widget([
                'form' => $form,
                'model' => $model,
                'attribute' => 'access_begin_datetime',
                'pickerType' => 'date',
            ]) ?>
            <?= \yadjet\datePicker\my97\DatePicker::widget([
                'form' => $form,
                'model' => $model,
                'attribute' => 'access_end_datetime',
                'pickerType' => 'date',
            ]) ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
