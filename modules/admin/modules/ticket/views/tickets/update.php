<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\ticket\models\Ticket */

$this->title = '更新: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '工单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="ticket-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
