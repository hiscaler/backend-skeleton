<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\finance\models\Finance */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '财务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="finance-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
