<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MetaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Meta');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
<div class="meta-index">

    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    Pjax::begin([
        'formSelector' => '#form-meta-search',
    ]);
    ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table table-striped'
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number'],
            ],
            [
                'attribute' => 'object_name_formatted',
                'contentOptions' => ['class' => 'meta-object-name center'],
            ],
            [
                'attribute' => 'key',
                'contentOptions' => ['class' => 'meta-key'],
            ],
            [
                'attribute' => 'label',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model['label'] . '<span>' . $model['description'] . '</span>';
                },
                'contentOptions' => ['class' => 'meta-label'],
            ],
            [
                'attribute' => 'input_type_text',
                'contentOptions' => ['class' => 'meta-input-type center'],
            ],
            'input_candidate_value',
            [
                'attribute' => 'return_value_type_text',
                'contentOptions' => ['class' => 'meta-return-value-type center'],
            ],
            [
                'attribute' => 'default_value',
                'contentOptions' => ['class' => 'meta-default-value'],
            ],
            [
                'attribute' => 'enabled',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean pointer enabled-handler'],
            ],
            // 'created_by',
            // 'created_at',
            // 'updated_by',
            // 'updated_at',
            // 'deleted_by',
            // 'deleted_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>

<?php \app\modules\admin\components\JsBlock::begin() ?>
<script type="text/javascript">
    yadjet.actions.toggle("table td.enabled-handler img", "<?= Url::toRoute('toggle') ?>");
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
