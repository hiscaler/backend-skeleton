<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\modules\slide\models\SlideSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column" style="display: none">
    <div class="slide-search form">
        <?php
        $form = ActiveForm::begin([
            'id' => 'form-slide-search',
            'action' => ['index'],
            'method' => 'get',
        ]);
        ?>
        <div class="entry">
            <?= $form->field($model, 'category_id')->dropDownList(\app\models\Category::tree('slide.module.category'), ['prompt' => '']) ?>

            <?= $form->field($model, 'title') ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'enabled')->dropDownList(\app\models\Option::booleanOptions(), ['prompt' => '']) ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
