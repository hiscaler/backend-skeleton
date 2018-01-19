<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wxpay\models\Order */

$this->title = $outTradeNo;
$this->params['breadcrumbs'][] = ['label' => '微信支付订单查询结果', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<?php if ($data['trade_state'] == 'SUCCESS'): ?>
    <div class="order-view">
        <?= DetailView::widget([
            'model' => $data,
            'attributes' => [
                [
                    'attribute' => 'trade_state',
                    'label' => '交易状态',
                ],
                [
                    'attribute' => 'transaction_id',
                    'label' => '微信订单号',
                ],
            ],
        ]) ?>
    </div>
<?php else: ?>
    <?= DetailView::widget([
        'model' => $data,
        'attributes' => [
            [
                'attribute' => 'trade_state',
                'label' => '交易状态',
            ],
            [
                'attribute' => 'trade_state_desc',
                'label' => '交易状态描述',
            ],
        ],
    ]) ?>
<?php endif; ?>
