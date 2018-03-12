<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Access Statistic Site Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="access-statistic-site-log-index">
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            'site.name',
            'ip',
            'referrer',
            [
                'attribute' => 'access_datetime',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
                'headerOptions' => ['class' => 'buttons-2 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
