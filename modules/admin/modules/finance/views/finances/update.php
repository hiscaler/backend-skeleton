<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\finance\models\Finance */

$this->title = '更新: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '财务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="finance-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
