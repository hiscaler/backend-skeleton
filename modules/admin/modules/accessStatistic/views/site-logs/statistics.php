<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '分类统计';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => '导出为 Excel', 'url' => ['statistics-to-excel', 'beginDatetime' => $beginDatetime, 'endDatetime' => $endDatetime, 'hours' => $hours]],
];
?>
<div class="access-statistic-site-log-index">
    <?= $this->render('_staticstics_search', ['beginDatetime' => $beginDatetime, 'endDatetime' => $endDatetime, 'hours' => $hours]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'ip',
                'header' => 'IP 地址',
                'contentOptions' => ['class' => 'ip-address'],
            ],
            [
                'attribute' => 'first_access_datetime',
                'header' => '首次访问时间',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime']
            ],
            [
                'attribute' => 'last_access_datetime',
                'header' => '最后访问时间',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime']
            ],
            [
                'attribute' => 'count',
                'header' => '访问次数',
            ],
        ],
    ]); ?>
</div>
