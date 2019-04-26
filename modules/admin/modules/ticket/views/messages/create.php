<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\ticket\models\TicketMessage */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '工单消息', 'url' => ['index', 'ticketId' => $ticketId]];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'ticketId' => $ticketId]],
]
?>
<div class="ticket-message-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
