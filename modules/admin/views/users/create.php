<?php
/* @var $this yii\web\View */
/* @var $model app\models\User */
$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => Yii::t('model', 'User'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="user-create">
    <?=
    $this->render('_form', [
        'model' => $model,
        'metaItems' => $metaItems,
        'dynamicModel' => $dynamicModel,
    ])
    ?>
</div>
