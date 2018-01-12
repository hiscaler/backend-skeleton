<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Member */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]],
];
?>
<div class="member-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            'username',
            'nickname',
            'avatar:image',
            'email:email',
            'tel',
            'mobile_phone',
            'register_ip',
            'login_count',
            'last_login_ip',
            'last_login_time:datetime',
            'status',
            'remark:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>
