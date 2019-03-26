<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\wechat\models\PayOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '企业付款订单管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
<div class="pay-order-index">
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            'mch_appid',
            'mchid',
            'device_info',
            'nonce_str',
            //'sign',
            //'partner_trade_no',
            //'payment_no',
            //'transfer_time:datetime',
            //'payment_time:datetime',
            //'openid',
            //'check_name',
            //'re_user_name',
            //'amount',
            //'desc',
            //'spbill_create_ip',
            //'status',
            //'reason',
            //'created_at',
            //'created_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
