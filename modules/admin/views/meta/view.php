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
//            'created_by',
                'created_at:datetime',
//            'updated_by',
                'updated_at:datetime',
//            'deleted_by',
                'deleted_at:datetime',
            ],
        ])
        ?>

        <div class="form-outside">
            <div class="form">
                <fieldset v-for="item in metaValidators">
                    <legend>
                        <input class="control-label" type="checkbox" id="meta-validator-name-{{ item.name }}" name="Meta[validatorsList][{{ item.name }}][name]" v-model="item.active" value="{{ item.name }}"/>
                        <label for="meta-validator-name-{{ item.name }}">{{ item.label }}</label>
                    </legend>
                    <div class="panel-body" v-if="!isEmptyObject(item.options)">
                        <ul class="list-group">
                            <li class="list-group-item" v-for="cfg in item.options">
                                <div class="form-group">
                                    <label>{{ item.messages[$key] }}</label>
                                    <input class="form-control" type="text" name="Meta[validatorsList][{{ item.name }}][options][{{ $key }}]" value="{{ cfg }}"/>
                                </div>
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

<?php \app\modules\admin\components\JsBlock::begin() ?>
    <script type="text/javascript">
        yadjet.urls = {
            validators: '<?= \yii\helpers\Url::toRoute(['api/validators']) ?>',
            meta: {
                validators: '<?= \yii\helpers\Url::toRoute(['api/meta-validators', 'metaId' => $model['id']]) ?>'
            }
        };
        Vue.http.get(yadjet.urls.validators).then((res) = > {
            vm.validators = res.data;
        })
        ;
        Vue.http.get(yadjet.urls.meta.validators).then((res) = > {
            vm.meta.validators = res.data;
        })
        ;
    </script>
<?php \app\modules\admin\components\JsBlock::end() ?>