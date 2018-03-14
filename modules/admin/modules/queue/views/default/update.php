<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\news\models\PostRaw */

$this->title = 'Update PostRaw: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => Yii::t('post.model', 'PostRaw'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]]
];
?>
<div class="news-update">
    <?= $this->render('_form', [
        'model' => $model,
        'newsContent' => $newsContent,
        'dynamicModel' => $dynamicModel,
    ]) ?>
</div>
