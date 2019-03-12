<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\notice\models\Notice */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '通知管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]]
];
?>
<div class="classic-case-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category.name',
            'title',
            'description:ntext',
            'content:raw',
            'enabled:boolean',
            'clicks_count',
            'published_at:datetime',
            'ordering',
            'created_at:datetime',
            'creater.nickname',
            'updated_at:datetime',
            'updater.nickname',
        ],
    ]) ?>
</div>
