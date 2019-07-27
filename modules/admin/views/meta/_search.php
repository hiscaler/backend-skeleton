<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MetaSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column">
    <div class="meta-search form">
        <?php
        $form = ActiveForm::begin([
            'id' => 'form-meta-search',
            'action' => ['index'],
            'method' => 'get',
        ]);
        ?>
        <div class="entry">
            <?= $form->field($model, 'table_name')->dropDownList(app\models\Option::models(), ['prompt' => '']) ?>

            <?= $form->field($model, 'key') ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'label') ?>

            <?= $form->field($model, 'enabled')->dropDownList(app\models\Option::boolean(), ['prompt' => '']) ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
