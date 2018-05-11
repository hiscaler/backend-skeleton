<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\WheelAward */

$this->title = '更新';
$this->params['breadcrumbs'][] = ['label' => '奖品设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id, 'wheelId' => $model['wheel_id']]];
$this->params['breadcrumbs'][] = '更新';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'wheelId' => $model['wheel_id']]],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create', 'wheelId' => $model['wheel_id']]],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]]
];
?>
<div class="wheel-award-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
