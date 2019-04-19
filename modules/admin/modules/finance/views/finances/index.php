<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\finance\models\FinanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '财务管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];
?>
<div class="finance-index">
    <?php Pjax::begin(); ?>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            switch ($model->type) {
                case \app\modules\admin\modules\finance\models\Finance::TYPE_INCOME:
                    $class = 'type-income';
                    break;

                case \app\modules\admin\modules\finance\models\Finance::TYPE_DISBURSE:
                    $class = 'type-disburse';
                    break;

                case \app\modules\admin\modules\finance\models\Finance::TYPE_REFUND:
                    $class = 'type-refund';
                    break;

                default:
                    $class = null;
                    break;
            }

            return [
                'class' => $class,
            ];
        },
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'member.username',
                'contentOptions' => ['class' => 'username'],
            ],
            [
                'attribute' => 'type',
                'format' => 'financeType',
                'contentOptions' => ['class' => 'type']
            ],
            [
                'attribute' => 'money',
                'format' => 'yuan',
                'contentOptions' => ['class' => 'number']
            ],

            [
                'attribute' => 'source',
                'format' => 'financeSource',
                'contentOptions' => ['style' => 'width: 60px;', 'class' => 'center']
            ],
            [
                'attribute' => 'status',
                'format' => 'financeStatus',
                'contentOptions' => ['style' => 'width: 60px;', 'class' => 'center']
            ],
            'remark:ntext',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'headerOptions' => ['class' => 'button-1 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php \app\modules\admin\components\CssBlock::begin() ?>
<style type="text/css">
    .type-income td.type,
    .type-disburse td.type,
    .type-refund td.type {
        width: 60px;
        text-align: center;
        color: #fff;
        font-weight: bold;
    }

    .type-income td.type {
        color: green;
    }

    .type-disburse td.type,
    .type-refund td.type {
        color: red;
    }
</style>
<?php \app\modules\admin\components\CssBlock::end() ?>
