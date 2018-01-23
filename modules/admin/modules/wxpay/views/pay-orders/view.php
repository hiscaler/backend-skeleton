<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wxpay\models\PayOrder */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '企业付款订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-order-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'mch_appid',
            'mchid',
            'device_info',
            'nonce_str',
            'sign',
            'partner_trade_no',
            'payment_no',
            'transfer_time:datetime',
            'payment_time:datetime',
            'openid',
            'check_name',
            're_user_name',
            'amount',
            'desc',
            'spbill_create_ip',
            'status',
            'reason',
            'created_at',
            'created_by',
        ],
    ]) ?>
</div>
