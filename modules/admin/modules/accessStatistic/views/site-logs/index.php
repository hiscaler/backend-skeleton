<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '访问统计站点日志管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => '导出为 Excel', 'url' => ['to-excel']],
];
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
            [
                'attribute' => 'referrer',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<a href="javascript:;" class="btn-copy" data-clipboard-target="#referrer-' . $model['id'] . '" title="复制">&nbsp;</a><span id="referrer-' . $model['id'] . '">' . $model['referrer'] . '</span>';
                },
            ],
            'browser',
            'browser_lang',
            'os',
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
