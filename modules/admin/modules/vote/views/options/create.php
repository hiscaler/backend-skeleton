<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\vote\models\VoteOption */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '投票管理', 'url' => ['votes/index']];
$this->params['breadcrumbs'][] = ['label' => "{$vote->title} 投票选项设置", 'url' => ['votes/index', 'voteId' => $vote['id']]];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'voteId' => $model['vote_id']]],
];
?>
<div class="vote-option-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
