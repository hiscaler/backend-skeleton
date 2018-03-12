<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\accessStatistic\models\AccessStatisticSite */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '访问统计站点管理', 'url' => ['index']];
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
