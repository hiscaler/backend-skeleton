<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\link\models\LinkSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column">
    <div class="link-search form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>
        <div class="entry">
            <?= $form->field($model, 'category_id')->dropDownList(\app\models\Category::tree('feedback.module.category', \app\models\Category::RETURN_TYPE_PRIVATE), ['prompt' => '']) ?>
            <?= $form->field($model, 'type')->dropDownList(\app\modules\admin\modules\link\models\Link::typeOptions(), ['prompt' => '']) ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'title') ?>
            <?= $form->field($model, 'enabled')->dropDownList(\app\models\Option::boolean(), ['prompt' => '']) ?>
        </div>
        <?php // echo $form->field($model, 'url') ?>

        <?php // echo $form->field($model, 'url_open_target') ?>

        <?php // echo $form->field($model, 'logo') ?>

        <?php // echo $form->field($model, 'ordering') ?>



        <?php // echo $form->field($model, 'created_at') ?>

        <?php // echo $form->field($model, 'created_by') ?>

        <?php // echo $form->field($model, 'updated_at') ?>

        <?php // echo $form->field($model, 'updated_by') ?>
        <div class="form-group buttons">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
