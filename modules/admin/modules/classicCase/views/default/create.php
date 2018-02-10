<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\classicCase\models\ClassicCase */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => '案例管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="classic-case-create">
    <?= $this->render('_form', [
        'model' => $model,
        'dynamicModel' => $dynamicModel,
    ]) ?>
</div>
