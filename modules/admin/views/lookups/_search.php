<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LookupSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column" style="display: none">
    <div class="form lookup-search">
        <?php
        $form = ActiveForm::begin([
            'id' => 'form-lookups',
            'action' => ['index'],
            'method' => 'get',
        ]);
        ?>
        <div class="entry">
            <?= $form->field($model, 'key') ?>

            <?= $form->field($model, 'label') ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'return_type')->dropDownList(\app\models\Lookup::returnTypeOptions(), ['prompt' => '']) ?>
            <?php echo $form->field($model, 'enabled')->dropDownList(\app\models\Option::boolean(), ['prompt' => '']) ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
