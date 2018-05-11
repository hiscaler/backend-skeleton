<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\Wheel */

$this->title = '更新';
$this->params['breadcrumbs'][] = ['label' => '大转盘', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]]
];
?>
<div class="wheel-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
