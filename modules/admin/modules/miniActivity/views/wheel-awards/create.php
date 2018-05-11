<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\WheelAward */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '奖品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'wheelId' => $model->wheel->id]],
];
?>
<div class="wheel-award-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
