<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\Wheel */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-layout-column">
    <div class="wheel-form form">
        <?php $form = ActiveForm::begin([
            'options' => [
                'enctype' => 'multipart/form-data',
            ]
        ]); ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        <div class="entry">
            <?= $form->field($model, 'win_message')->textInput(['maxlength' => true]) ?>
            <?php // $form->field($model, 'get_award_message')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="entry">
            <?= \yadjet\datePicker\my97\DatePicker::widget([
                'form' => $form,
                'model' => $model,
                'attribute' => 'begin_datetime',
                'pickerType' => 'datetime',
            ]) ?>

            <?= \yadjet\datePicker\my97\DatePicker::widget([
                'form' => $form,
                'model' => $model,
                'attribute' => 'end_datetime',
                'pickerType' => 'datetime',
            ]) ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'photo')->fileInput() ?>
        </div>
        <?= $form->field($model, 'repeat_play_message')->textInput(['maxlength' => true]) ?>
        <div class="entry">
            <?= $form->field($model, 'background_image')->fileInput() ?>
            <?php // $form->field($model, 'background_image_repeat_type')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'finished_title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'finished_description')->textarea(['rows' => 6]) ?>
        </div>
        <?= $form->field($model, 'finished_photo')->fileInput() ?>
        <div class="entry">
            <?= $form->field($model, 'estimated_people_count')->textInput() ?>

            <?= $form->field($model, 'actual_people_count')->textInput() ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'show_awards_quantity')->checkbox() ?>

            <?= $form->field($model, 'play_times_per_person')->textInput() ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'play_limit_type')->dropDownList(\app\modules\admin\modules\miniActivity\models\Wheel::playLimitTypeOptions()) ?>

            <?= $form->field($model, 'play_times_per_person_by_limit_type')->textInput() ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'win_times_per_person')->textInput() ?>

            <?= $form->field($model, 'win_interval_seconds')->textInput() ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'ordering')->dropDownList(\app\models\Option::ordering()) ?>

            <?= $form->field($model, 'enabled')->checkbox() ?>
        </div>
        <?= $form->field($model, 'blocks_count')->dropDownList(\app\models\Option::ordering(1, 24)) ?>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
