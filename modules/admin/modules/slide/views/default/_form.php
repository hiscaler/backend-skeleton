<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Slide */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-outside">
    <div class="slide-form form">

        <?php
        $form = ActiveForm::begin([
            'options' => [
                'enctype' => 'multipart/form-data',
            ],
        ]);
        ?>

        <?= $form->field($model, 'group_id')->textInput() ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'url_open_target')->dropDownList(\app\modules\slide\models\Slide::urlOpenTargetOptions()) ?>

        <?= $form->field($model, 'picture_path')->fileInput() ?>

        <?= $form->field($model, 'ordering')->dropDownList(\app\models\Option::orderingOptions()) ?>

        <?= $form->field($model, 'enabled')->checkBox([], null) ?>

        <div class="form-group buttons">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
