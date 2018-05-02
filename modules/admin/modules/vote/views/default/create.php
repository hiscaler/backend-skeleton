<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\vote\models\Vote */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '投票管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="vote-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
