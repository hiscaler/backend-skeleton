<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\vote\models\VoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '投票管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];

$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';
?>
<div class="vote-index">
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            'category_id',
            'title',
            'description:ntext',
            [
                'attribute' => 'begin_datetime',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime']
            ],
            [
                'attribute' => 'end_datetime',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime']
            ],
            [
                'attribute' => 'total_votes_count',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'allow_anonymous',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean']
            ],
            [
                'attribute' => 'allow_view_results',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean']
            ],
            [
                'attribute' => 'allow_multiple_choice',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean']
            ],
            [
                'attribute' => 'interval_seconds',
                'contentOptions' => ['class' => 'number'],
            ],
            //'items:ntext',
            //'ordering',
            [
                'attribute' => 'enabled',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean']
            ],
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {options} {update} {delete}',
                'buttons' => [
                    'options' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a(Html::img($baseUrl . '/images/vote-options.png'), ['options/index', 'voteId' => $model['id']], ['data-pjax' => 0, 'title' => '投票选项']);
                    }
                ],
                'headerOptions' => ['class' => 'buttons-4 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
