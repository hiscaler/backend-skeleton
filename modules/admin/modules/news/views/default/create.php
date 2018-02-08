<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\news\models\News */

$this->title = 'Create News';
$this->params['breadcrumbs'][] = ['label' => 'News', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="news-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
