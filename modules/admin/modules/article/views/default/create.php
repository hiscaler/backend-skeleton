<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\article\models\Article */

$this->title = '添加文章';
$this->params['breadcrumbs'][] = ['label' => '文章管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="article-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
