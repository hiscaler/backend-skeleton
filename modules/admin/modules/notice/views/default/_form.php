<?php

use yadjet\editor\UEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\notice\models\Notice */
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
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>
        <div class="tab-panel" id="tab-panel-basic">
            <?php
            $categories = \app\models\Category::tree('classicCase.module.category');
            if ($categories) {
                echo $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => '']);
            }
            ?>

            <?php
            $entityLabels = \app\models\Label::getItems(false, true);
            if ($entityLabels):
                ?>
                <fieldset class="entity-labels">
                    <legend><?= Yii::t('app', 'Entity Labels') ?></legend>
                    <?php
                    foreach ($entityLabels as $key => $labels) {
                        echo $form->field($model, 'entityLabels')->checkboxList($labels, [])->label($key);
                    }
                    ?>
                </fieldset>
            <?php endif; ?>
            <div class="entry">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'view_permission')->dropDownList(\app\modules\admin\modules\notice\models\Notice::viewPermissionOptions()) ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                <?= UEditor::widget([
                    'form' => $form,
                    'model' => $model,
                    'attribute' => 'content',
                ]) ?>
            </div>
            <div class="entry">
                <?= \yadjet\datePicker\my97\DatePicker::widget([
                    'form' => $form,
                    'model' => $model,
                    'attribute' => 'published_at',
                    'pickerType' => 'datetime',
                ]) ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'enabled')->checkbox(null, false) ?>
                <?= $form->field($model, 'ordering')->dropDownList(\app\models\Option::ordering()) ?>
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