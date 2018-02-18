<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\feedback\models\FeedbackSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column" style="display: none">
    <div class="feedback-search form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>

        <?php
        if ($categories) {
            echo $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => '']);
        }
        ?>
        <div class="entry">
            <?= $form->field($model, 'title') ?>

            <?= $form->field($model, 'username') ?>
        </div>
        <?php // echo $form->field($model, 'mobile_phone') ?>

        <?php // echo $form->field($model, 'email') ?>

        <?php // echo $form->field($model, 'message') ?>

        <?php // echo $form->field($model, 'created_at') ?>

        <?php // echo $form->field($model, 'created_by') ?>

        <?php // echo $form->field($model, 'updated_at') ?>

        <?php // echo $form->field($model, 'updated_by') ?>
        <div class="form-group buttons">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
