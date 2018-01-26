<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wechat\models\Order */

$this->title = $outTradeNo;
$this->params['breadcrumbs'][] = ['label' => '微信订单查询结果', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div style="padding-top: 10px;">
    <ul class="tabs-common">
        <li class="active"><a href="javascript:;" data-toggle="tab-panel-order-query">订单查询结果</a></li>
        <?php if (isset($refund['refund_count'])): ?>
            <li><a href="javascript:;" data-toggle="tab-panel-refund-query">订单退款结果<em class="badges badges-red"><?= $refund['refund_count'] ?></em></a></li>
        <?php endif; ?>
    </ul>
    <div class="panels">
        <div class="tab-panel" id="tab-panel-order-query">
            <?php if ($payment['result_code'] == 'SUCCESS'): ?>
                <?php if ($payment['trade_state'] == 'SUCCESS'): ?>
                    <div class="order-view">
                        <?= DetailView::widget([
                            'model' => $payment,
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
                        'model' => $payment,
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
            <?php else: ?>
                <div class="notice"><?= "查询失败，失败原因：[ {$refund['err_code']} ] {$refund['err_code_des']}" ?></div>
            <?php endif; ?>
        </div>
        <div class="tab-panel" id="tab-panel-refund-query" style="display: none;">
            <?php if ($refund['result_code'] == 'SUCCESS'): ?>
                <?php if ($refund['refund_count']): ?>
                    <div class="grid-view">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>商户退款订单号</th>
                                <th>微信退款订单号</th>
                                <th>金额</th>
                                <th class="last">时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php for ($i = 0; $i < $refund['refund_count']; $i++): ?>
                                <tr>
                                    <td class="serial-number"><?= $i + 1 ?></td>
                                    <td><?= $refund['out_refund_no_' . $i] ?></td>
                                    <td><?= $refund['refund_id_' . $i] ?></td>
                                    <td class="number"><?= $refund['refund_fee_' . $i] / 100 ?></td>
                                    <td class="datetime"><?= $refund['refund_success_time_' . $i] ?></td>
                                </tr>
                            <?php endfor; ?>
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: bold;">总金额：</td>
                                <td class="number"><?= $refund['refund_fee'] / 100 ?></td>
                                <td>&nbsp;</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="notice">暂无退款。</div>
                <?php endif; ?>
            <?php else: ?>
                <div class="notice"><?= "查询失败，失败原因：[ {$refund['err_code']} ] {$refund['err_code_des']}" ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

