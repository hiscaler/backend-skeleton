<?php

use yadjet\editor\UEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\article\models\Article */
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
            <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'keyword')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?=
            UEditor::widget([
                'form' => $form,
                'model' => $model,
                'attribute' => 'content',
            ])
            ?>
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
