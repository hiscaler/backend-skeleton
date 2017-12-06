<?php
/* @var $this yii\web\View */
/* @var $model app\models\Label */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
        'modelClass' => Yii::t('model', 'Labels'),
    ]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Labels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name . ' [ ' . Yii::t('app', 'Update') . ' ]';

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']]
];
?>
<div class="attribute-update">

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
