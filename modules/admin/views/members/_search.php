<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MemberSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column">
    <div class="form member-search">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>
        <div class="entry">
            <?= $form->field($model, 'username') ?>

            <?= $form->field($model, 'mobile_phone') ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'type')->dropDownList(\app\models\Member::typeOptions(), ['prompt' => '']) ?>
            <?= $form->field($model, 'status')->dropDownList(\app\models\Member::statusOptions(), ['prompt' => '']) ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
