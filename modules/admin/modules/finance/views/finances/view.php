<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\finance\models\Finance */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '财务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="finance-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type:financeType',
            'money:yuan',
            'source:financeSource',
            'remittance_slip:image',
            'related_key',
            'status:financeStatus',
            'remark:ntext',
            'member.username',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>
