<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\link\models\Link */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '友情链接管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="friendly-link-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
