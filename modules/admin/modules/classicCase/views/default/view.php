<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\classicCase\models\ClassicCase */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '案例管理', 'url' => ['index']];
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
            'keywords',
            'description:ntext',
            'content:raw',
            'picture_path:image',
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
