<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\wechat\models\PayOrder */

$this->title = 'Create Pay Order';
$this->params['breadcrumbs'][] = ['label' => '企业付款订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-order-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
