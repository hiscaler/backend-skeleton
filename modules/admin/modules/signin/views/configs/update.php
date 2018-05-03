<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\signin\models\SigninCreditConfig */

$this->title = '更新';
$this->params['breadcrumbs'][] = ['label' => '签到设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];
?>
<div class="signin-credit-config-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
