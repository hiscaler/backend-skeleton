<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MemberLoginLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '会员登录日志', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="member-login-log-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'member.username',
            'ip',
            'login_at:datetime',
            'client_information',
        ],
    ]) ?>
</div>
