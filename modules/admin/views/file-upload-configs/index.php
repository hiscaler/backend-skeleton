<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\FileUploadConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'File Upload Configs');
$this->params['breadcrumbs'][] = Yii::t('app', 'File Upload Configs');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
<div class="upload-config-index">

    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    Pjax::begin([
        'formSelector' => '#form-upload-configs-search',
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number'],
            ],
            [
                'attribute' => 'type_text',
                'contentOptions' => ['class' => 'center', 'style' => 'width: 40px'],
            ],
            [
                'attribute' => 'model_name',
                'format' => 'modelName',
                'contentOptions' => ['class' => 'model-name center'],
            ],
            [
                'attribute' => 'attribute',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model['attribute'], ['update', 'id' => $model['id']]);
                },
                'contentOptions' => ['class' => 'file-upload-config-attribute']
            ],
            'extensions',
            [
                'attribute' => 'size',
                'value' => function ($model) {
                    return $model['min_size'] / 1024 . 'KB ~ ' . $model['max_size'] / 1024 . 'KB';
                },
                'contentOptions' => ['class' => 'file-upload-config-size center']
            ],
            [
                'attribute' => 'thumb',
                'value' => function ($model) {
                    return ($model['thumb_width'] && $model['thumb_height']) ? $model['thumb_width'] . ' x ' . $model['thumb_height'] : '';
                },
                'contentOptions' => ['class' => 'file-upload-config-thumb center']
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
            [
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
            ],
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
