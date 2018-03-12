<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLog */

$this->title = 'Create Access Statistic Site Log';
$this->params['breadcrumbs'][] = ['label' => 'Access Statistic Site Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="access-statistic-site-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
