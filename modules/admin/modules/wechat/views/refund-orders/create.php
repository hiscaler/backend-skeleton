<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wechat\models\RefundOrder */

$this->title = 'Create Order Refund';
$this->params['breadcrumbs'][] = ['label' => 'Order Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-refund-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
