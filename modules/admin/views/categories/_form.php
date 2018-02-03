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
            <?= $form->field($model, 'sign')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'parent_id')->dropDownList(Category::tree(null, Category::RETURN_TYPE_ALL), ['prompt' => '']) ?>
            <?php
            $template = '{label}{input}{thumb}{error}';
            $thumb = '';
            if (!$model->isNewRecord && $model->icon) {
                $thumb = '<img class="thumbnail" src="' . Yii::$app->getRequest()->getBaseUrl() . $model->icon . '" />';
            }
            $template = str_replace('{thumb}', $thumb, $template);
            echo $form->field($model, 'icon', ['template' => $template])->fileInput() ?>
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