<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Meta */
/* @var $form yii\widgets\ActiveForm */
?>
<div>
    <ul class="tabs-common">
        <li class="active"><a href="javascript:;" data-toggle="tab-panel-basic">基本设定</a></li>
        <li><a href="javascript:;" data-toggle="tab-panel-rules">验证规则</a></li>
    </ul>
    <div class="panels form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="tab-panel" id="tab-panel-basic">
            <div class="entry">
                <?= $form->field($model, 'object_name')->dropDownList(app\models\Meta::getObjectNames()) ?>

                <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'input_type')->dropDownList(\app\models\Meta::inputTypeOptions()) ?>

                <?= $form->field($model, 'return_value_type')->dropDownList(\app\models\Meta::returnValueTypeOptions()) ?>
            </div>
            <?= $form->field($model, 'input_candidate_value')->textarea() ?>
            <div class="entry">
                <?= $form->field($model, 'default_value')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'enabled')->checkbox([], false) ?>
            </div>
        </div>
        <div class="tab-panel" id="tab-panel-rules" style="display: none;">
            <fieldset v-for="item in metaValidators">
                <legend>
                    <input class="control-label" type="checkbox" id="meta-validator-name-{{ item.name }}" name="Meta[validatorsList][{{ item.name }}][name]" v-model="item.active" value="{{ item.name }}" />
                    <label for="meta-validator-name-{{ item.name }}">{{ item.label }}</label>
                </legend>
                <div class="panel-body" v-if="!isEmptyObject(item.options)">
                    <ul class="list-group">
                        <li class="list-group-item" v-for="cfg in item.options">
                            <div class="form-group">
                                <label>{{ item.messages[$key] }}</label>
                                <input class="form-control" type="text" name="Meta[validatorsList][{{ item.name }}][options][{{ $key }}]" value="{{ cfg }}" />
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="notice" v-else>
                    暂无其他特定规则
                </div>
            </fieldset>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php \app\modules\admin\components\JsBlock::begin() ?>
<script type="text/javascript">
    yadjet.urls = {
        validators: '<?= \yii\helpers\Url::toRoute(['api/validators']) ?>',
        meta: {
            validators: '<?= \yii\helpers\Url::toRoute(['api/meta-validators', 'metaId' => $model['id']]) ?>'
        }
    };
    axios.get(yadjet.urls.validators, {})
        .then(function (response) {
            vm.validators = response.data;
        })
        .catch(function (error) {
            console.log(error)
            vm.validators = [];
        });
    axios.get(yadjet.urls.meta.validators, {})
        .then(function (response) {
            vm.meta.validators = response.data;
        })
        .catch(function (error) {
            console.log(error)
            vm.meta.validators = [];
        });
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
