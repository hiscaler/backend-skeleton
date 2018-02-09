<?php

use yadjet\editor\UEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\news\models\News */
/* @var $form yii\widgets\ActiveForm */
?>
<div>
    <ul class="tabs-common">
        <li class="active"><a href="javascript:;" data-toggle="tab-panel-basic">基本数据</a></li>
        <?php if ($dynamicModel->getMetaOptions()): ?>
            <li><a href="javascript:;" data-toggle="tab-panel-metas">扩展属性</a></li>
        <?php endif; ?>
    </ul>
    <div class="panels form form-layout-column">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->errorSummary($model) ?>
        <div class="tab-panel" id="tab-panel-basic">
            <?= $form->field($model, 'category_id')->dropDownList(\app\models\Category::tree('news.module.category'), ['prompt' => '']) ?>

            <?php
            $entityAttributes = \app\models\Label::getItems(false, true);
            if ($entityAttributes):
                ?>
                <fieldset>
                    <legend><?= Yii::t('app', 'Entity Attributes') ?></legend>
                    <?php
                    foreach ($entityAttributes as $key => $attributes) {
                        echo $form->field($model, 'entityAttributes')->checkboxList($attributes, ['unselect' => null])->label($key);
                    }
                    ?>
                </fieldset>
            <?php endif; ?>
            <div class="entry">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'short_title')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'author')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>
            </div>
            <?= UEditor::widget([
                'form' => $form,
                'model' => $newsContent,
                'attribute' => 'content',
            ]) ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            <div class="entry">
                <?= $form->field($model, 'source')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'source_url')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'picture_path')->fileInput() ?>
                <?= \yadjet\datePicker\my97\DatePicker::widget([
                    'form' => $form,
                    'model' => $model,
                    'attribute' => 'published_at',
                    'pickerType' => 'datetime',
                ]) ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'enabled')->checkbox([]) ?>

                <?= $form->field($model, 'enabled_comment')->checkbox([]) ?>
            </div>
        </div>
        <div class="tab-panel" id="tab-panel-metas" style="display: none">
            <?php foreach ($dynamicModel->getMetaOptions() as $metaItem): ?>
                <?= $form->field($dynamicModel, $metaItem['key'])->{$metaItem['input_type']}(['value' => $metaItem['value']])->label($metaItem['label']) ?>
            <?php endforeach; ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>