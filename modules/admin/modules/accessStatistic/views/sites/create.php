<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\accessStatistic\models\AccessStatisticSite */

$this->title = 'Create Access Statistic Site';
$this->params['breadcrumbs'][] = ['label' => 'Access Statistic Sites', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="access-statistic-site-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
