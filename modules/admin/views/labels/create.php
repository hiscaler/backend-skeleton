<?php
/* @var $this yii\web\View */
/* @var $model app\models\Label */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => Yii::t('model', 'Label'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Labels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];
?>
<div class="attribute-create">
    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>
</div>
