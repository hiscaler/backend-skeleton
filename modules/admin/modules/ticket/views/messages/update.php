<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\ticket\models\TicketMessage */

$this->title = '更新: ' . $model->ticket->title;
$this->params['breadcrumbs'][] = ['label' => '工单消息', 'url' => ['index', 'ticketId' => $model->ticket_id]];
$this->params['breadcrumbs'][] = ['label' => $model->ticket->title, 'url' => ['view', 'id' => $model->id, 'ticketId' => $model->ticket_id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="ticket-message-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
