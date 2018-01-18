<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wxpay\models\Order */

$this->title = $model->out_trade_no;
$this->params['breadcrumbs'][] = ['label' => '微信支付订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
            'total_fee',
            'spbill_create_ip',
            'time_start:datetime',
            'time_expire:datetime',
            'goods_tag',
            'trade_type',
            'product_id',
            'limit_pay',
            'openid',
            'status',
        ],
    ]) ?>
</div>
