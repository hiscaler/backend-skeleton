<?php
/* @var $this yii\web\View */
/* @var $model app\models\Lookup */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Lookup',
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lookups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']]
];
?>
<div class="lookup-create">
    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>
</div>
