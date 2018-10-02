<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\feedback\models\FeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '留言反馈管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
<div class="feedback-index">
    <?php Pjax::begin(); ?>
    <?= $this->render('_search', ['model' => $searchModel, 'categories' => $categories]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'category.name',
                'contentOptions' => ['class' => 'category-name'],
                'visible' => $categories,
            ],
            'title',
            [
                'attribute' => 'username',
                'contentOptions' => ['class' => 'username'],
            ],
            [
                'attribute' => 'tel',
                'contentOptions' => ['class' => 'tel'],
            ],
            [
                'attribute' => 'mobile_phone',
                'contentOptions' => ['class' => 'mobile-phone'],
            ],
            [
                'attribute' => 'email',
                'contentOptions' => ['class' => 'email'],
            ],
            [
                'attribute' => 'enabled',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean'],
            ],
            [
                'attribute' => 'created_at',
                'format' => 'date',
                'contentOptions' => ['class' => 'date']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {reply} {delete}',
                'buttons' => [
                    'reply' => function ($url, $model, $key) {
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-add-child"></span>', ['reply', 'id' => $model['id']], ['data-pjax' => 0, 'title' => '回复']);
                    }
                ],
                'headerOptions' => array('class' => 'buttons-3 last'),
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
