<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\vote\models\VoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column" style="display: none">
    <div class="vote-search form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>

        <?= $form->field($model, 'id') ?>

        <?= $form->field($model, 'category_id') ?>

        <?= $form->field($model, 'title') ?>

        <?= $form->field($model, 'description') ?>

        <?= $form->field($model, 'begin_datetime') ?>

        <?php // echo $form->field($model, 'end_datetime') ?>

        <?php // echo $form->field($model, 'votes_count') ?>

        <?php // echo $form->field($model, 'allow_anonymous') ?>

        <?php // echo $form->field($model, 'allow_view_results') ?>

        <?php // echo $form->field($model, 'allow_multiple_choice') ?>

        <?php // echo $form->field($model, 'interval_seconds') ?>

        <?php // echo $form->field($model, 'items') ?>

        <?php // echo $form->field($model, 'ordering') ?>

        <?php // echo $form->field($model, 'enabled') ?>

        <?php // echo $form->field($model, 'created_at') ?>

        <?php // echo $form->field($model, 'created_by') ?>

        <?php // echo $form->field($model, 'updated_at') ?>

        <?php // echo $form->field($model, 'updated_by') ?>
        <div class="form-group buttons">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
