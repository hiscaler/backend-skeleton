<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\miniActivity\models\MiniActivityWheelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '大转盘';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];

$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';
?>
<div class="wheel-index">
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            'title',
            [
                'attribute' => 'begin_datetime',
                'format' => 'date',
                'contentOptions' => ['class' => 'date'],
            ],
            [
                'attribute' => 'end_datetime',
                'format' => 'date',
                'contentOptions' => ['class' => 'date'],
            ],
            //'description:ntext',
            //'photo',
            //'repeat_play_message',
            //'background_image',
            //'background_image_repeat_type',
            //'finished_title',
            //'finished_description:ntext',
            //'finished_photo',
            //'awards_setting:ntext',
            [
                'attribute' => 'estimated_people_count',
                'contentOptions' => ['class' => 'number']
            ],
            [
                'attribute' => 'actual_people_count',
                'contentOptions' => ['class' => 'number']
            ],
            //'play_times_per_person:datetime',
            //'play_limit_type',
            //'play_times_per_person_by_limit_type:datetime',
            //'win_times_per_person:datetime',
            //'win_interval_seconds',
            //'show_awards_quantity',
            //'ordering',
            [
                'attribute' => 'enabled',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean'],
            ],
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {awards} {logs} {update} {delete}',
                'buttons' => [
                    'awards' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a(Html::img($baseUrl . '/images/awards.png'), ['wheel-awards/index', 'wheelId' => $model['id']], ['data-pjax' => 0, 'title' => '奖品设置']);
                    },
                    'logs' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a(Html::img($baseUrl . '/images/logs.png'), ['wheel-logs/index', 'wheelId' => $model['id']], ['data-pjax' => 0, 'title' => '日志']);
                    }
                ],
                'headerOptions' => ['class' => 'buttons-5 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
