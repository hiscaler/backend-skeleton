<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\signin\models\SigninCreditConfig */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '签到设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="signin-credit-config-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
