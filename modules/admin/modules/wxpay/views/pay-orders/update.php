<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wxpay\models\PayOrder */

$this->title = 'Update Pay Order: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => '企业付款订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pay-order-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
