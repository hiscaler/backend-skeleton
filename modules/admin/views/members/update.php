<?php
/* @var $this yii\web\View */
/* @var $model app\models\Slide */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
        'modelClass' => Yii::t('model', 'User'),
    ]) . ' ' . $model->username;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model['id']]],
];
?>
<div class="slide-update">

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
