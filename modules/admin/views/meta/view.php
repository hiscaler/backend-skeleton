<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Meta */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Meta'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]],
];
?>
    <div>
        <ul class="tabs-common">
            <li class="active"><a href="javascript:;" data-toggle="tab-panel-basic">基本设定</a></li>
            <li><a href="javascript:;" data-toggle="tab-panel-rules">验证规则</a></li>
        </ul>
        <div class="panels">
            <div class="tab-panel" id="tab-panel-basic">
                <div class="meta-view">
                    <?=
                    DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'object_name_formatted',
                            'key',
                            'label',
                            'description',
                            'input_type_text',
                            'return_value_type_text',
                            'default_value',
                            'enabled:boolean',
                            'created_at:datetime',
                            'updated_at:datetime',
                            'deleted_at:datetime',
                        ],
                    ])
                    ?>
                </div>
            </div>
            <div class="tab-panel clearfix" id="tab-panel-rules" style="display: none">
                <div class="form">
                    <fieldset class="model-rule" v-for="item in metaValidators" v-show="item.active">
                        <legend>
                            <input disabled="disabled" class="control-label" type="checkbox" id="meta-validator-name-{{ item.name }}" name="Meta[validatorsList][{{ item.name }}][name]" v-model="item.active" value="{{ item.name }}" />
                            <label for="meta-validator-name-{{ item.name }}">{{ item.label }}</label>
                        </legend>
                        <div class="panel-body" v-if="!isEmptyObject(item.options)">
                            <ul class="list-group">
                                <li class="list-group-item" v-for="cfg in item.options">
                                    <label for="">{{ item.messages[$key] }}</label>：{{cfg}}
                                </li>
                            </ul>
                        </div>
                        <div class="notice" v-else>
                            暂无其他特定规则
                        </div>
                    </fieldset>
                </div>
            </div>
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