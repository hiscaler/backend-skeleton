<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\classicCase\models\ClassicCase */

$this->title = 'Update Classic Case: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => '案例管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]]
];
?>
<div class="classic-case-update">
    <?= $this->render('_form', [
        'model' => $model,
        'dynamicModel' => $dynamicModel,
    ]) ?>
</div>
