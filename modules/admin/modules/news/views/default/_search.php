<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\news\models\NewsSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column" style="display: none">
    <div class="news-search form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>

        <?= $form->field($model, 'id') ?>

        <?= $form->field($model, 'category_id') ?>

        <?= $form->field($model, 'title') ?>


        <?php // echo $form->field($model, 'description') ?>

        <?php // echo $form->field($model, 'author') ?>

        <?php // echo $form->field($model, 'source') ?>

        <?php // echo $form->field($model, 'source_url') ?>

        <?php // echo $form->field($model, 'is_picture_news') ?>

        <?php // echo $form->field($model, 'picture_path') ?>

        <?php // echo $form->field($model, 'enabled') ?>

        <?php // echo $form->field($model, 'enabled_comment') ?>

        <?php // echo $form->field($model, 'comments_count') ?>

        <?php // echo $form->field($model, 'published_at') ?>

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
