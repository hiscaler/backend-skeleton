<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\classicCase\models\ClassicCaseSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column" style="display: none">
    <div class="classic-case-search form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>
        <div class="entry">
            <?php
            $categories = \app\models\Category::tree('classicCase.module.category');
            if ($categories) {
                echo $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => '']);
            }
            ?>

            <?= $form->field($model, 'title') ?>
        </div>
        <?= $form->field($model, 'enabled')->dropDownList(\app\models\Option::booleanOptions(), ['prompt' => '']) ?>

        <?php // echo $form->field($model, 'published_at') ?>
        <div class="form-group buttons">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
