<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\vote\models\VoteOptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '投票选项';
$this->params['breadcrumbs'][] = ['label' => '投票管理', 'url' => ['votes/index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'voteId' => $vote['id']]],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create', 'voteId' => $vote['id']]],
];
?>
<div class="vote-option-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'ordering',
                'contentOptions' => ['class' => 'ordering']
            ],
            [
                'attribute' => 'title',
                'contentOptions' => ['style' => 'width: 200px']
            ],
            'description:ntext',
            //'photo',
            [
                'attribute' => 'votes_count',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'enabled',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
