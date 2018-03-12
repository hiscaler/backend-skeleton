<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\accessStatistic\models\AccessStatisticSite */

$this->title = 'Update Access Statistic Site: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Access Statistic Sites', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]]
];
?>
<div class="access-statistic-site-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
