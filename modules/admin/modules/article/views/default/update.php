<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\article\models\Article */

$this->title = 'Update Article: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => '文章管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]]
];
?>
<div class="article-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
