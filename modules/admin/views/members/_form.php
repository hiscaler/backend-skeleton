<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Member */
/* @var $form yii\widgets\ActiveForm */
?>
<div>
    <ul class="tabs-common">
        <li class="active"><a href="javascript:;" data-toggle="tab-panel-basic">基本数据</a></li>
        <?php if ($dynamicModel->getMetaOptions()): ?>
            <li><a href="javascript:;" data-toggle="tab-panel-metas">扩展属性</a></li>
        <?php endif; ?>
    </ul>
    <div class="panels form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="tab-panel" id="tab-panel-basic">
            <?php // $form->field($model, 'type')->textInput() ?>

            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

            <?php if ($model->isNewRecord): ?>
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'confirm_password')->passwordInput(['maxlength' => true]) ?>
            <?php endif; ?>

            <?php
            $template = '{label}{input}{thumb}{error}';
            $thumb = '';
            if (!$model->isNewRecord && $model->avatar) {
                $thumb = '<img class="thumbnail" src="' . Yii::$app->getRequest()->getBaseUrl() . $model->avatar . '" />';
            }
            $template = str_replace('{thumb}', $thumb, $template);
            echo $form->field($model, 'avatar', ['template' => $template])->fileInput() ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'tel')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'status')->dropDownList(\app\models\Member::statusOptions()) ?>

            <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
        </div>
        <div class="tab-panel" id="tab-panel-metas" style="display: none">
            <?php foreach ($dynamicModel->getMetaOptions() as $metaItem): ?>
                <?= $form->field($dynamicModel, $metaItem['key'])->{$metaItem['input_type']}(['value' => $metaItem['value']])->label($metaItem['label']) ?>
            <?php endforeach; ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
