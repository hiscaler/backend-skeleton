<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\wxpay\models\OrderRefundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单退款';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
<div class="order-refund-index">
    <?php Pjax::begin(); ?>
    <?= $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            'nonce_str',
            [
                'attribute' => 'out_trade_no',
                'format' => 'raw',
                'value' => function ($model) {
                    return \yii\helpers\Html::a($model['out_trade_no'], ['view', 'id' => $model['id']]);
                },
                'contentOptions' => ['style' => 'width: 140px;'],
            ],
            [
                'attribute' => 'out_refund_no',
                'format' => 'raw',
                'value' => function ($model) {
                    return \yii\helpers\Html::a($model['out_refund_no'], ['view', 'id' => $model['id']]);
                },
                'contentOptions' => ['style' => 'width: 140px;'],
            ],
            [
                'attribute' => 'total_fee',
                'value' => function ($model) {
                    return $model['total_fee'] / 100;
                },
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'refund_id',
            ],
            [
                'attribute' => 'refund_fee',
                'value' => function ($model) {
                    return $model['refund_fee'] / 100;
                },
                'contentOptions' => ['class' => 'number'],
            ],
            'refund_desc',
            'refund_account',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime']
            ],
            [
                'attribute' => 'creater.nickname',
                'contentOptions' => ['class' => 'username']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {query}',
                'buttons' => [
                    'query' => function ($url, $model, $key) {
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-query"></span>', $url, ['pjax' => 0, 'class' => 'order-query', 'data-key' => $model['id'], 'title' => '微信商户平台订单查询']);
                    },
                ],
                'headerOptions' => ['class' => 'buttons-2 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
