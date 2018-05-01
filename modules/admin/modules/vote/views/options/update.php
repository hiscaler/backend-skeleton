<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\vote\models\VoteOption */

$this->title = '更新';
$this->params['breadcrumbs'][] = ['label' => '投票管理', 'url' => ['votes/index']];
$this->params['breadcrumbs'][] = ['label' => "{$model->vote->title} 投票选项", 'url' => ['index', 'voteId' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'voteId' => $model['vote_id']]],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create', 'voteId' => $model['vote_id']]],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]]
];
?>
<div class="vote-option-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
