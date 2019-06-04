<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\exam\models\QuestionBankSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '题库管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => '列表', 'url' => ['index']],
    ['label' => '添加', 'url' => ['create']],
];
?>
<div class="question-bank-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>
    <?php Pjax::begin(); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'name',
                'value' => function ($model) {
                    return "{$model->name} [ {$model->description} ]";
                },
            ],
            [
                'attribute' => 'questions_count',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'participation_times',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'status',
                'format' => 'questionBankStatus',
                'contentOptions' => ['class' => 'boolean'],
            ],
            [
                'attribute' => 'created_at',
                'format' => 'date',
                'contentOptions' => ['class' => 'date'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'date',
                'contentOptions' => ['class' => 'date'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {questions} {to-word} {update} {delete}',
                'buttons' => [
                    'questions' => function ($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-add-training-log"]);

                        return Html::a($icon, ['questions/index', 'questionBankId' => $model->id]);
                    },
                    'to-word' => function ($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-save"]);

                        return Html::a($icon, ['to-word', 'id' => $model->id], ['title' => '导出为 Word 文件', 'target' => '_blank', 'data-pjax' => 0]);
                    }
                ],
                'headerOptions' => ['class' => 'btn-5 last'],
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
