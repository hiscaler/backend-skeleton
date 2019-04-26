<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\ticket\models\TicketMessage */

$this->title = $model->ticket->title;
$this->params['breadcrumbs'][] = ['label' => '工单消息', 'url' => ['index', 'ticketId' => $model->ticket_id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'ticketId' => $model->ticket_id]],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create', 'ticketId' => $model->ticket_id]],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id, 'ticketId' => $model->ticket_id]],
]
?>
<div class="ticket-message-view">
    <?= DetailView::widget(['model' => $model,
        'attributes' => [
            'id',
            'ticket.title',
            'content:ntext',
            'member.username',
            'reply_username',
            'created_at:datetime',
        ],
    ])
    ?>
</div>
