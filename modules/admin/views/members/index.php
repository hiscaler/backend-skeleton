<?php

use app\models\Member;
use app\modules\admin\components\GridView;
use app\modules\admin\components\MessageBox;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Members');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => '统计', 'url' => ['statistics']],
    ['label' => Yii::t('app', 'Grid Column Config'), 'url' => ['grid-column-configs/index', 'name' => Member::class, 'id' => 'grid-view-members'], 'htmlOptions' => ['class' => 'grid-column-config', 'data-grid-id' => 'grid-view-members']],
];

$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';
$defaultAvatar = $baseUrl . '/images/default-avatar.jpg';
?>
<div class="member-index">
    <?= $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    $session = Yii::$app->getSession();
    if ($session->hasFlash('notice')) {
        echo MessageBox::widget([
            'title' => Yii::t('app', 'Prompt Message'),
            'message' => $session->getFlash('notice'),
            'showCloseButton' => true
        ]);
    }
    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'grid-view-members',
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'type',
                'format' => 'memberType',
                'contentOptions' => ['class' => 'member-type'],
            ],
            [
                'attribute' => 'role',
                'format' => 'memberRole',
                'contentOptions' => ['class' => 'member-role'],
            ],
            [
                'attribute' => 'category_id',
                'value' => function ($model) {
                    return $model->category ? $model->category->name : null;
                },
                'contentOptions' => ['style' => 'width: 60px;'],
            ],
            [
                'attribute' => 'username',
                'format' => 'raw',
                'value' => function ($model) use ($defaultAvatar) {
                    $avatar = $model['avatar'] ?: $defaultAvatar;

                    return Html::img($avatar, ['class' => 'avatar']) . Html::a($model['username'], ['view', 'id' => $model['id']], ['class' => 'member-type-' . $model['type']]);
                },
                'contentOptions' => ['class' => 'login-account'],
            ],
            [
                'attribute' => 'nickname',
                'contentOptions' => ['class' => 'username'],
            ],
            [
                'attribute' => 'real_name',
                'contentOptions' => ['class' => 'username'],
            ],
            'email:email',
            [
                'attribute' => 'mobile_phone',
                'contentOptions' => ['class' => 'mobile-phone'],
            ],
            // 'register_ip',
            [
                'attribute' => 'login_count',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'total_money',
                'format' => 'yuan',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'available_money',
                'format' => 'yuan',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'total_credits',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'available_credits',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'alarm_credits',
                'value' => function ($model) {
                    return $model->alarm_credits ?: null;
                },
                'contentOptions' => ['class' => 'number'],
            ],
            // 'last_login_ip',
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
                'attribute' => 'expired_datetime',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            // 'remark:ntext',
            [
                'attribute' => 'status',
                'format' => 'memberStatus',
                'contentOptions' => ['class' => 'data-status'],
            ],
            [
                'attribute' => 'usable_scope',
                'format' => 'memberUsableScope',
                'contentOptions' => ['style' => 'width: 60px; text-align: center'],
            ],
            // 'created_by',
            // 'updated_at',
            // 'updated_by',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {change-password} {delete}',
                'buttons' => [
                    'change-password' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a(Html::img($baseUrl . '/images/change-password.png'), $url, ['title' => Yii::t('app', 'Change Password')]);
                    },
                ],
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
