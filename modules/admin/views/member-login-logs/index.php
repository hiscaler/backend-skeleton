<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MemberLoginLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员登录日志';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => '清理全部日志', 'url' => ['clean'], 'htmlOptions' => ['data-confirm' => '您确定要清理掉所有登录日志？', 'data-method' => 'post']],
];
?>
<div class="member-login-log-index">
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
                'attribute' => 'member.username',
                'contentOptions' => ['class' => 'username'],
            ],
            [
                'attribute' => 'ip',
                'contentOptions' => ['class' => 'ip-address'],
            ],
            [
                'attribute' => 'login_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            'client_information',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
                'headerOptions' => array('class' => 'buttons-2 last'),
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
