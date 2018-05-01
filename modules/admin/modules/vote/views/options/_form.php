<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\vote\models\VoteOption */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-layout-column">
    <div class="vote-option-form form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ordering')->dropDownList(\app\models\Option::ordering()) ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'photo')->fileInput() ?>

        <?= $form->field($model, 'votes_count')->textInput() ?>

        <?= $form->field($model, 'enabled')->checkbox() ?>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
