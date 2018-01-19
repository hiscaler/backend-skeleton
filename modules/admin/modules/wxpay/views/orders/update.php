<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wxpay\models\Order */

$this->title = 'Update Order: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => '微信支付订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->out_trade_no, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]]
];
?>
<div class="order-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
