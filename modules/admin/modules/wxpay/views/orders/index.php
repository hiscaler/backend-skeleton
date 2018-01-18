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
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'appid',
            'mch_id',
            'device_info',
            'nonce_str',
            //'sign',
            //'sign_type',
            //'transaction_id',
            //'out_trade_no',
            //'body',
            //'detail:ntext',
            //'attach',
            //'fee_type',
            //'total_fee',
            //'spbill_create_ip',
            //'time_start:datetime',
            //'time_expire:datetime',
            //'goods_tag',
            //'trade_type',
            //'product_id',
            //'limit_pay',
            //'openid',
            //'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
