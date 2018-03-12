<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '访问统计站点日志管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="access-statistic-site-log-index">
    <?php Pjax::begin(); ?>
    <?= $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'site.name',
                'contentOptions' => ['style' => 'width: 80px;']
            ],
            [
                'attribute' => 'ip',
                'contentOptions' => ['class' => 'ip-address']
            ],
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
