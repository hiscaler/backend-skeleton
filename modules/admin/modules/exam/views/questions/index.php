<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\exam\models\QuestionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '题目管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => '列表', 'url' => ['index', 'questionBankId' => $questionBankId]],
    ['label' => '添加', 'url' => ['create', 'questionBankId' => $questionBankId]],
];
?>
<div class="question-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>
    <?php Pjax::begin(); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number'],
            ],
            [
                'attribute' => 'type',
                'format' => 'questionType',
                'contentOptions' => ['style' => 'width: 60px; text-align: center']
            ],
            [
                'attribute' => 'status',
                'format' => 'questionStatus',
                'contentOptions' => ['class' => 'boolean'],
            ],
            'content:ntext',
            'options:ntext',
            'answer:ntext',
            'resolve:ntext',
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
