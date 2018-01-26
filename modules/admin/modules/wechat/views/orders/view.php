<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wechat\models\Order */

$this->title = $model->out_trade_no;
$this->params['breadcrumbs'][] = ['label' => '微信支付订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="order-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'appid',
            'mch_id',
            'device_info',
            'nonce_str',
            'sign',
            'sign_type',
            'transaction_id',
            'out_trade_no',
            'body',
            'detail:ntext',
            'attach',
            'fee_type',
            [
                'attribute' => 'total_fee',
                'value' => $model['total_fee'] / 100
            ],
            'spbill_create_ip',
            'time_start:datetime',
            'time_expire:datetime',
            'time_end:datetime',
            'goods_tag',
            'trade_type',
            'product_id',
            'limit_pay',
            'openid',
            'status:orderStatus',
        ],
    ]) ?>
</div>
