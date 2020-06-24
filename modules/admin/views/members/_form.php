<?php

use app\models\Member;
use app\modules\admin\modules\wuliu\models\DxmAccount;
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
        <?= $form->errorSummary($model) ?>
        <div class="tab-panel" id="tab-panel-basic">
            <div class="entry">
                <?= $form->field($model, 'type')->dropDownList(Member::typeOptions()) ?>
                <?php
                if ($model->getIsNewRecord()) {
                    echo $form->field($model, 'parent_id')->dropDownList(Member::map(), ['prompt' => '']);
                }
                ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'category_id')->dropDownList(DxmAccount::map(), ['prompt' => '']) ?>
            </div>
            <?php
            $options = ['maxlength' => true];
            !$model->getIsNewRecord() && $options['disabled'] = 'disabled';
            ?>
            <div class="entry">
                <?= $form->field($model, 'username')->textInput($options); ?>
                <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'real_name')->textInput(['maxlength' => true]) ?>
                <?php
                $template = '{label}{input}{thumb}{error}';
                $thumb = '';
                if (!$model->isNewRecord && $model->avatar) {
                    $thumb = '<img class="thumbnail" src="' . Yii::$app->getRequest()->getBaseUrl() . $model->avatar . '" />';
                }
                $template = str_replace('{thumb}', $thumb, $template);
                echo $form->field($model, 'avatar', ['template' => $template])->fileInput() ?>
            </div>
            <?php if ($model->isNewRecord): ?>
                <div class="entry">
                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'confirm_password')->passwordInput(['maxlength' => true]) ?>
                </div>
            <?php endif; ?>
            <div class="entry">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]) ?>
            </div>
            <?= \yadjet\datePicker\my97\DatePicker::widget([
                'form' => $form,
                'model' => $model,
                'attribute' => 'expired_datetime',
                'pickerType' => 'datetime',
            ]) ?>

            <?= $form->field($model, 'role_list')->checkboxList($model::roleOptions(), $model->roles) ?>
            <div class="entry">
                <?= $form->field($model, 'usable_scope')->dropDownList(Member::usableScopeOptions()) ?>

                <?= $form->field($model, 'status')->dropDownList(Member::statusOptions()) ?>
            </div>
            <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
        </div>
        <div class="tab-panel" id="tab-panel-metas" style="display: none">
            <?php
            foreach ($dynamicModel->getMetaOptions() as $metaItem) {
                $inputType = $metaItem['input_type'];
                switch ($inputType) {
                    case 'dropDownList':
                        echo $form->field($dynamicModel, $metaItem['key'])->$inputType($metaItem['input_candidate_value'], ['value' => $metaItem['value'], 'prompt' => ''])->label($metaItem['label']);
                        break;

                    default:
                        echo $form->field($dynamicModel, $metaItem['key'])->$inputType(['value' => $metaItem['value']])->label($metaItem['label']);
                        break;
                }
            }
            ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
