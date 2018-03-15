<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '队列任务管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">
    <?php // $this->render('_search', ['model' => $searchModel, 'categories' => $categories]); ?>
    <?php Pjax::begin([
        'formSelector' => '#form-post',
        'linkSelector' => '#grid-view-post a',
    ]); ?>
    <?= GridView::widget([
        'id' => 'grid-view-post',
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'channel',
                'header' => '频道',
                'contentOptions' => ['style' => 'width: 80px', 'class' => 'center']
            ],
            [
                'attribute' => 'job',
                'header' => '任务',
                'format' => 'raw',
                'value' => function ($model) {
                    $obj = unserialize($model['job']);
                    $vars = [];
                    foreach (get_object_vars($obj) as $key => $value) {
                        $vars[] = "<em class='badges badges-gray'>$key: $value</em>";
                    }

                    return "<em class='badges badges-red'>{$model['id']}</em> " . get_class($obj) . PHP_EOL . implode(PHP_EOL, $vars);
                }
            ],
            [
                'attribute' => 'ttr',
                'header' => 'TTR',
                'contentOptions' => ['style' => 'width: 30px;', 'class' => 'center']
            ],
            [
                'attribute' => 'delay',
                'header' => '延时（秒）',
                'contentOptions' => ['style' => 'width: 70px;', 'class' => 'center']
            ],
            [
                'attribute' => 'priority',
                'header' => '权重',
                'contentOptions' => ['style' => 'width: 40px;', 'class' => 'center']
            ],
            [
                'attribute' => 'pushed_at',
                'header' => '入队时间',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'reserved_at',
                'header' => '上次运行时间',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'attempt',
                'header' => '尝试次数',
                'contentOptions' => ['class' => 'number center'],
            ],
            [
                'attribute' => 'done_at',
                'header' => '完成时间',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'headerOptions' => ['class' => 'button-1 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
