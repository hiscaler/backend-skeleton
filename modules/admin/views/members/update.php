<?php

/* @var $this yii\web\View */
/* @var $model app\models\Member */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
        'modelClass' => Yii::t('model', 'Member'),
    ]) . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]],
];
?>
<div class="member-update">
    <?= $this->render('_form', [
        'model' => $model,
        'dynamicModel' => $dynamicModel,
    ]) ?>
</div>
