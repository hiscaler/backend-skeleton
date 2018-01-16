<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Members');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];
?>
<div class="member-index">
    <?= $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'avatar',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::img($model['avatar'], ['class' => 'avatar']), ['view', 'id' => $model['id']]);
                },
                'contentOptions' => ['class' => 'avatar'],
            ],
            [
                'attribute' => 'username',
                'contentOptions' => ['class' => 'username'],
            ],
            [
                'attribute' => 'nickname',
                'contentOptions' => ['class' => 'username'],
            ],
            'email:email',
            [
                'attribute' => 'tel',
                'contentOptions' => ['class' => 'tel'],
            ],
            [
                'attribute' => 'mobile_phone',
                'contentOptions' => ['class' => 'mobile-phone'],
            ],
            // 'register_ip',
            [
                'attribute' => 'login_count',
                'contentOptions' => ['class' => 'number'],
            ],
            // 'last_login_ip',
            // 'last_login_time:datetime',
            // 'status',
            // 'remark:ntext',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            // 'created_by',
            // 'updated_at',
            // 'updated_by',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
