<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\news\models\News */

$this->title = 'Update News: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'News', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]]
];
?>
<div class="news-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
