<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\link\models\Link */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-layout-column">
    <div class="friendly-link-form form">
        <?php $form = ActiveForm::begin([
            'options' => [
                'enctype' => 'multipart/form-data',
            ]
        ]); ?>

        <?= $form->field($model, 'category_id')->dropDownList(\app\models\Category::tree('link', \app\models\Category::RETURN_TYPE_PRIVATE), ['prompt' => '']) ?>

        <?= $form->field($model, 'type')->dropDownList(\app\modules\admin\modules\link\models\Link::typeOptions()) ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'url_open_target')->dropDownList(\app\modules\admin\modules\link\models\Link::urlOpenTargetOptions()) ?>

        <?= $form->field($model, 'logo')->fileInput() ?>

        <?= $form->field($model, 'ordering')->dropDownList(\app\models\Option::orderingOptions()) ?>

        <?= $form->field($model, 'enabled')->checkbox()->label(false) ?>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
