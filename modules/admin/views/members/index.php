<?php

use app\modules\admin\components\MessageBox;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';

$this->title = Yii::t('app', 'Members');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>

<div class="user-index">

    <?= $this->render('_search', ['model' => $searchModel]) ?>

    <?php
    $session = Yii::$app->getSession();
    if ($session->hasFlash('notice')) {
        echo MessageBox::widget([
            'title' => Yii::t('app', 'Prompt Message'),
            'message' => $session->getFlash('notice'),
            'showCloseButton' => true
        ]);
    }

    Pjax::begin([
        'formSelector' => '#form-user-search',
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'username',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model['username'], ['update', 'id' => $model['id']]);
                },
                'contentOptions' => ['class' => 'username']
            ],
            [
                'attribute' => 'nickname',
                'contentOptions' => ['class' => 'username']
            ],
            [
                'attribute' => 'credits_count',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'user_group_text',
                'contentOptions' => ['class' => 'user-group'],
            ],
            [
                'attribute' => 'login_count',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'last_login_time',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'status_text',
                'contentOptions' => ['class' => 'data-status']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {change-password}',
                'buttons' => [
                    'change-password' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a(Html::img($baseUrl . '/images/change-password.png'), $url, ['data-pjax' => 0]);
                    }
                ],
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]);
    Pjax::end();
    ?>

</div>