<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\ticket\models\TicketMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '工单消息';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'ticketId' => $ticketId]],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create', 'ticketId' => $ticketId]]
];

$baseUrl = \Yii::$app->getRequest()->getBaseUrl() . '/admin';
?>
<div class="ticket-message-index">
    <?php Pjax::begin(); ?>
    <?= $this->render('_search', ['model' => $searchModel, 'ticketId' => $ticketId]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            'ticket.title',
            'content:ntext',
            [
                'attribute' => 'member.username',
                'contentOptions' => ['class' => 'username'],
            ],
            [
                'attribute' => 'reply_username',
                'contentOptions' => ['class' => 'username'],
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['messages/view', 'id' => $model->id, 'ticketId' => $model->id], ['title' => '查看详情']);
                    },
                    'update' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['messages/update', 'id' => $model->id, 'ticketId' => $model->id], ['title' => '更新']);
                    },
                    'delete' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['messages/delete', 'id' => $model->id, 'ticketId' => $model->id], [
                            'title' => '删除',
                            'data-pjax' => 0,
                            'data-method' => 'post',
                            'data-confirm' => '您确定要删除此项吗？',
                        ]);
                    },
                ],
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
