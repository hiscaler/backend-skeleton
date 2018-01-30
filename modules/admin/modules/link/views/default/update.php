<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\link\models\Link */

$this->title = 'Update Friendly Link: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => '友情链接管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]]
];
?>
<div class="link-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
