<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\miniActivity\models\WheelAwardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '奖品管理';
$this->params['breadcrumbs'][] = ['label' => '大转盘', 'url' => ['index', 'wheelId' => $wheel['id']]];
$this->params['breadcrumbs'][] = ['label' => "{$wheel->title} 奖品管理", 'url' => ['index', 'wheelId' => $wheel['id']]];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'wheelId' => $wheel['id']]],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create', 'wheelId' => $wheel['id']]],
];
?>
<div class="wheel-award-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'ordering',
                'contentOptions' => ['class' => 'ordering'],
            ],
            'title',
            'description:ntext',
            //'photo',
            //'total_quantity',
            //'remaining_quantity',
            //'enabled',

            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
