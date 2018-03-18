<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Categories');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];
$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';
?>
<div class="categories-index">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
                'data-tt-id' => $model['id'],
                'class' => $model['enabled'] ? 'enabled' : 'disabled',
                'data-tt-parent-id' => $model['parent_id'],
                'style' => $model['parent_id'] ? 'display: none' : '',
            ];
        },
        'columns' => [
            [
                'attribute' => 'name',
                'header' => Yii::t('category', 'Name'),
                'format' => 'raw',
                'value' => function ($model) {
                    return "<span class=\"pk\">[ {$model['ordering']} ]</span>" . Html::a($model['name'], ['update', 'id' => $model['id']]) . ' <span class="badges badges-gray">' . $model['short_name'] . '</span>' . ' <span class="badges badges-gray">#' . $model['id'] . '</span>' . ($model['sign'] ? ' <span class="badges badges-red">' . $model['sign'] . '</span>' : '') . '<span class="alias">' . $model['alias'] . '</span>';
                },
            ],
            [
                'header' => Yii::t('category', 'Icon'),
                'attribute' => 'icon',
                'format' => 'image',
                'contentOptions' => ['class' => 'icon-img']
            ],
            [
                'attribute' => 'description',
                'header' => Yii::t('category', 'Description'),
                'value' => function ($model) {
                    return \yii\helpers\StringHelper::truncate($model['description'], 30);
                }
            ],
            [
                'attribute' => 'enabled',
                'header' => Yii::t('app', 'Enabled'),
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean pointer enabled-handler'],
            ],
            [
                'attribute' => 'created_by',
                'header' => Yii::t('app', 'Created By'),
                'value' => function ($model) {
                    return $model['creater']['nickname'];
                },
                'contentOptions' => ['class' => 'username']
            ],
            [
                'attribute' => 'created_at',
                'header' => Yii::t('app', 'Created At'),
                'format' => 'date',
                'contentOptions' => ['class' => 'date']
            ],
            [
                'attribute' => 'updated_by',
                'header' => Yii::t('app', 'Updated By'),
                'value' => function ($model) {
                    return $model['updater']['nickname'];
                },
                'contentOptions' => ['class' => 'username']
            ],
            [
                'attribute' => 'updated_at',
                'header' => Yii::t('app', 'Updated At'),
                'format' => 'date',
                'contentOptions' => ['class' => 'date']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{create} {update} {delete}',
                'buttons' => [
                    'create' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a('<span class="glyphicon glyphicon-add-child"></span>', ['create', 'parentId' => $model['id']], ['data-pjax' => 0, 'title' => '添加子分类']);
                    }
                ],
                'headerOptions' => array('class' => 'buttons-2 last'),
            ],
        ],
    ]);
    ?>
</div>
<?php
$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin/jquery-treetable-3.2.0';
$this->registerCssFile($baseUrl . '/css/jquery.treetable.css');
$this->registerCssFile($baseUrl . '/css/jquery.treetable.theme.default.css');
$this->registerJsFile($baseUrl . '/jquery.treetable.js', [
    'depends' => ['\yii\web\JqueryAsset']
]);

\app\modules\admin\components\JsBlock::begin();
?>
<script type="text/javascript">
    yadjet.actions.toggle("table td.enabled-handler img", "<?= yii\helpers\Url::toRoute('toggle') ?>");
    $(".table").treetable({expandable: true, initialState: "expand"});
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
