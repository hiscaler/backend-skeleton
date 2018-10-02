<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\feedback\models\Feedback */

$this->title = $model->title ?: $model->id;
$this->params['breadcrumbs'][] = ['label' => '留言反馈管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="feedback-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'category.name',
                'visible' => $model->category_id,
            ],
            'title',
            'username',
            'tel',
            'mobile_phone',
            'email:email',
            'message:ntext',
            'response_message:ntext',
            'response_datetime:datetime',
            'enabled:boolean',
            'created_at:datetime',
            'creater.nickname',
            'updated_at:datetime',
            'updater.nickname',
        ],
    ]) ?>
</div>
