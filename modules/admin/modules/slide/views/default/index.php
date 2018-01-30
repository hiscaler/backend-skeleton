<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SlideSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Slides');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
<div class="slide-index">


    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    Pjax::begin([
        'formSelector' => '#form-slide-search',
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
                'attribute' => 'ordering',
                'contentOptions' => ['class' => 'ordering'],
            ],
            [
                'attribute' => 'group_name',
                'contentOptions' => ['class' => 'group-name'],
            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<span class=\"pk\">[ {$model['id']} ]</span>" . Html::a($model['title'], ['view', 'id' => $model['id']]);
                }
            ],
            [
                'attribute' => 'url',
                'format' => 'url',
                'contentOptions' => ['class' => 'url'],
            ],
            [
                'attribute' => 'url_open_target_text',
                'contentOptions' => ['class' => 'url-open-target center'],
            ],
            [
                'attribute' => 'enabled',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean pointer enabled-handler'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
