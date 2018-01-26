<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\wechat\models\RefundOrderSearch */
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
//            'nonce_str',
            [
                'attribute' => 'out_refund_no',
                'format' => 'raw',
                'value' => function ($model) {
                    return \yii\helpers\Html::a($model['out_refund_no'], ['view', 'id' => $model['id']]);
                },
                'contentOptions' => ['style' => 'width: 140px;'],
            ],
            [
                'attribute' => 'out_trade_no',
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
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-query"></span>', $url, ['pjax' => 0, 'class' => 'order-query', 'data-key' => $model['id'], 'title' => '微信商户平台退款查询']);
                    },
                ],
                'headerOptions' => ['class' => 'buttons-2 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php \app\modules\admin\components\JsBlock::begin() ?>
<script type="text/javascript">
    $(function () {
        // 微信商户平台订单查询
        var queryUrl = '<?= \yii\helpers\Url::toRoute(['orders/refund-query', 'id' => '_id']) ?>';
        $('.order-query').on('click', function () {
            var $t = $(this);
            layer.open({
                type: 2,
                title: '退款查询结果',
                area: ['400px', '150px'],
                shade: 0.8,
                closeBtn: 1,
                shadeClose: true,
                content: queryUrl.replace('_id', $t.attr('data-key'))
            });

            return false;
        });
    });
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
