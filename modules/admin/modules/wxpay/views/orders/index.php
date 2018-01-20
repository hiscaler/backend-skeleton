<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\wxpay\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '微信支付订单管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
<div class="order-index">
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
                'attribute' => 'out_trade_no',
                'format' => 'raw',
                'value' => function ($model) {
                    return \yii\helpers\Html::a($model['out_trade_no'], ['view', 'id' => $model['id']]);
                },
                'contentOptions' => ['style' => 'width: 140px;'],
            ],
//            'appid',
//            'mch_id',
//            'device_info',
            'nonce_str',
            //'sign',
            //'sign_type',
            [
                'attribute' => 'transaction_id',
                'contentOptions' => ['style' => 'width: 140px;'],
            ],

            [
                'attribute' => 'product_id',
                'contentOptions' => ['style' => 'width: 80px;']
            ],
            'body',
            //'detail:ntext',
            //'attach',
            [
                'attribute' => 'fee_type',
                'contentOptions' => ['style' => 'width: 40px; text-align: center']
            ],
            [
                'attribute' => 'total_fee',
                'value' => function ($model) {
                    return $model['total_fee'] / 100;
                },
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'spbill_create_ip',
                'contentOptions' => ['class' => 'ip-address']
            ],
            [
                'attribute' => 'time_start',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime']
            ],
            [
                'attribute' => 'time_expire',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime']
            ],
            //'goods_tag',
            //'trade_type',
            //'product_id',
            //'limit_pay',
            [
                'attribute' => 'openid',
                'contentOptions' => ['class' => 'openid']
            ],
            [
                'attribute' => 'status',
                'format' => 'orderStatus',
                'contentOptions' => ['style' => 'width: 80px;']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {query} {refund}',
                'buttons' => [
                    'query' => function ($url, $model, $key) {
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-query"></span>', $url, ['pjax' => 0, 'class' => 'order-query', 'data-key' => $model['id'], 'title' => '微信商户平台订单查询']);
                    },
                    'refund' => function ($url, $model, $key) {
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-refund"></span>', $url, ['pjax' => 0, 'class' => 'order-refund', 'data-key' => $model['id'], 'data-total-fee' => $model['total_fee'], 'title' => '退款']);
                    },
                ],
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php \app\modules\admin\components\JsBlock::begin() ?>
<script type="text/javascript">
    $(function () {
        // 微信商户平台订单查询
        var queryUrl = '<?= \yii\helpers\Url::toRoute(['orders/query', 'id' => '_id']) ?>';
        $('.order-query').on('click', function () {
            var $t = $(this);
            layer.open({
                type: 2,
                title: '订单查询结果',
                area: ['400px', '150px'],
                shade: 0.8,
                closeBtn: 1,
                shadeClose: true,
                content: queryUrl.replace('_id', $t.attr('data-key'))
            });
            
            return false;
        });

        // 微信商户平台退款
        var refundUrl = '<?= \yii\helpers\Url::toRoute(['orders/refund', 'id' => '_id', 'refundFee' => '_refundFee']) ?>';
        $('.order-refund').on('click', function () {
            var $t = $(this);
            layer.confirm('是否确认进行退款操作？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                layer.prompt({title: '请确认退款金额', formType: 0, value: $t.attr('data-total-fee') / 100}, function (refundFee, index) {
                    layer.close(index);
                    refundUrl = refundUrl.replace('_id', $t.attr('data-key')).replace('_refundFee', parseFloat(refundFee));
                    $.ajax({
                        type: "POST",
                        url: refundUrl,
                        dataType: "json",
                        success: function (response) {
                            if (response.success) {
                                layer.msg('退款操作成功。');
                            } else {
                                layer.alert(response.error.message);
                            }
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            layer.alert('[ ' + XMLHttpRequest.status + ' ] ' + XMLHttpRequest.responseText);
                        }
                    });
                });
            }, function () {
            });

            return false;
        });
    });
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
