<?php
/* @var $this yii\web\View */
/* @var $model app\models\Member */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => Yii::t('model', 'Member'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']]
];
?>
<div class="member-create">
    <?= $this->render('_form', [
        'model' => $model,
        'dynamicModel' => $dynamicModel,
    ]) ?>
</div>
