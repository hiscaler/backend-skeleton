<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\ticket\models\TicketSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '工单管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']]
];

$baseUrl = \Yii::$app->getRequest()->getBaseUrl() . '/admin';
?>
<div class="ticket-index">
    <?php Pjax::begin(); ?>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'category.name',
                'contentOptions' => ['style' => 'width: 120px;'],
            ],
            'title',
            'description:ntext',
            [
                'attribute' => 'mobile_phone',
                'contentOptions' => ['class' => 'mobile-phone'],
            ],
            [
                'attribute' => 'email',
                'contentOptions' => ['class' => 'email'],
            ],
            [
                'attribute' => 'status',
                'format' => 'ticketStatus',
                'contentOptions' => ['style' => 'width: 80px; text-align: center'],
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'creater.nickname',
                'contentOptions' => ['class' => 'username'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {messages} {update} {delete}',
                'buttons' => [
                    'messages' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a(Html::img($baseUrl . '/images/messages.png'), ['messages/index', 'ticketId' => $model->id], ['title' => '回复日志']);
                    },
                ],
                'headerOptions' => ['class' => 'buttons-4 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
