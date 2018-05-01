<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\vote\models\VoteOption */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '投票管理', 'url' => ['votes/index']];
$this->params['breadcrumbs'][] = ['label' => "{$model->vote->title} 投票选项", 'url' => ['index', 'voteId' => $model['vote_id']]];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'voteId' => $model['vote_id']]],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create', 'voteId' => $model['vote_id']]],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]]
];
?>
<div class="vote-option-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'ordering',
            'title',
            'description:ntext',
            'photo',
            'votes_count',
            'enabled:boolean',
        ],
    ]) ?>
</div>
