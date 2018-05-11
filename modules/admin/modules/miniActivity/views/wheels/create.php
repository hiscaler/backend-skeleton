<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\Wheel */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '大转盘', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="wheel-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
