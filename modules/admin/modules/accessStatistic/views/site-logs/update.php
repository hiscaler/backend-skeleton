<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLog */

$this->title = 'Update Access Statistic Site Log: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Access Statistic Site Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="access-statistic-site-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
