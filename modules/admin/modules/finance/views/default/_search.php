<?php

use app\modules\admin\modules\finance\models\Finance;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\finance\models\FinanceSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column">
    <div class="form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>
        <div class="entry">
            <?= $form->field($model, 'type')->dropDownList(Finance::typeOptions(), ['prompt' => '']) ?>

            <?= $form->field($model, 'source')->dropDownList(Finance::sourceOptions(), ['prompt' => '']) ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'member_username') ?>

            <?= $form->field($model, 'status')->dropDownList(Finance::statusOptions(), ['prompt' => '']) ?>
        </div>
        <div class="entry">
            <?= \yadjet\datePicker\my97\DatePicker::widget([
                'form' => $form,
                'model' => $model,
                'attribute' => 'begin_date',
                'pickerType' => 'date',
            ]) ?>
            <?= \yadjet\datePicker\my97\DatePicker::widget([
                'form' => $form,
                'model' => $model,
                'attribute' => 'end_date',
                'pickerType' => 'date',
            ]) ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-outline-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
