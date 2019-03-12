<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\notice\models\Notice */

$this->title = '更新';
$this->params['breadcrumbs'][] = ['label' => '通知管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

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
