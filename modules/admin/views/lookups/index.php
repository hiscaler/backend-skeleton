<?php

use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LookupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Lookups');
$this->params['breadcrumbs'][] = $this->title;
$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
<div class="lookup-index">
    <?= $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    Pjax::begin([
        'formSelector' => '#form-lookups',
        'linkSelector' => '#grid-view-lookups a',
    ]);
    echo \yii\grid\GridView::widget([
        'id' => 'grid-view-lookups',
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'key',
                'contentOptions' => ['style' => 'width: 120px;'],
            ],
            [
                'attribute' => 'label',
                'format' => 'raw',
                'value' => function ($model) {
                    return \yii\helpers\Html::a($model['label'], ['update', 'id' => $model['id']]);
                },
                'contentOptions' => ['class' => 'lookup-label'],
            ],
            'description',
            [
                'attribute' => 'value',
                'value' => function ($model) {
                    return StringHelper::truncate(unserialize($model['value']), 20);
                }
            ],
            [
                'attribute' => 'return_type_text',
                'contentOptions' => ['class' => 'lookup-return-type center'],
            ],
            [
                'attribute' => 'enabled',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean pointer lookup-enabled-handler'],
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model) {
                    return $model['creater']['nickname'];
                },
                'contentOptions' => ['class' => 'username']
            ],
            [
                'attribute' => 'created_at',
                'format' => 'date',
                'contentOptions' => ['class' => 'date']
            ],
            [
                'attribute' => 'updated_by',
                'value' => function ($model) {
                    return $model['updater']['nickname'];
                },
                'contentOptions' => ['class' => 'username']
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'date',
                'contentOptions' => ['class' => 'date']
            ],
            /* [
              'attribute' => 'deleted_by',
              'value' => function ($model) {
              return $model['deleter']['nickname'];
              },
              'contentOptions' => ['class' => 'username']
              ],
              [
              'attribute' => 'deleted_at',
              'format' => 'date',
              'contentOptions' => ['class' => 'date']
              ], */
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'headerOptions' => ['class' => 'buttons-2 last'],
            ],
        ],
    ]);
    Pjax::end();
    ?>
</div>
<?php
$this->registerJs('yadjet.actions.toggle("table td.lookup-enabled-handler img", "' . Url::toRoute('toggle') . '");');
?>
    