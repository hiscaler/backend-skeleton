<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\MiniActivityWheelSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="wheel-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'win_message') ?>

    <?= $form->field($model, 'get_award_message') ?>

    <?= $form->field($model, 'begin_datetime') ?>

    <?php // echo $form->field($model, 'end_datetime') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'photo') ?>

    <?php // echo $form->field($model, 'repeat_play_message') ?>

    <?php // echo $form->field($model, 'background_image') ?>

    <?php // echo $form->field($model, 'background_image_repeat_type') ?>

    <?php // echo $form->field($model, 'finished_title') ?>

    <?php // echo $form->field($model, 'finished_description') ?>

    <?php // echo $form->field($model, 'finished_photo') ?>

    <?php // echo $form->field($model, 'awards_setting') ?>

    <?php // echo $form->field($model, 'estimated_people_count') ?>

    <?php // echo $form->field($model, 'actual_people_count') ?>

    <?php // echo $form->field($model, 'play_times_per_person') ?>

    <?php // echo $form->field($model, 'play_limit_type') ?>

    <?php // echo $form->field($model, 'play_times_per_person_by_limit_type') ?>

    <?php // echo $form->field($model, 'win_times_per_person') ?>

    <?php // echo $form->field($model, 'win_interval_seconds') ?>

    <?php // echo $form->field($model, 'show_awards_quantity') ?>

    <?php // echo $form->field($model, 'ordering') ?>

    <?php // echo $form->field($model, 'enabled') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
