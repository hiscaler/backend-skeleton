<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wechat\models\RefundOrder */

$this->title = $model->out_refund_no;
$this->params['breadcrumbs'][] = ['label' => '退款管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="order-refund-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            'appid',
            'mch_id',
            'nonce_str',
            'sign',
            'sign_type',
            'transaction_id',
            'out_trade_no',
            'out_refund_no',
            [
                'attribute' => 'total_fee',
                'value' => $model['total_fee'] / 100
            ],
            'refund_id',
            [
                'attribute' => 'refund_fee',
                'value' => $model['refund_fee'] / 100
            ],
            'refund_fee_type',
            'refund_desc',
            'refund_account',
            'created_at:datetime',
            'creater.nickname',
        ],
    ]) ?>
</div>
