<?php

use app\models\Category;
use app\models\Option;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-outside">
    <div class="form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="entry">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="entry">
            <?= $form->field($model, 'parent_id')->dropDownList(Category::getTree($model->type, '顶级分类')) ?>

            <?= $form->field($model, 'icon_path')->fileInput() ?>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

        <div class="entry">
            <?= $form->field($model, 'ordering')->dropDownList(Option::orderingOptions()) ?>

            <?= $form->field($model, 'enabled')->checkbox([], false) ?>
        </div>

        <div class="form-group buttons">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>