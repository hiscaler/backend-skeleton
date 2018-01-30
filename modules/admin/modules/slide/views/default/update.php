<?php

/* @var $this yii\web\View */
/* @var $model app\models\Slide */

$this->title = 'Update Slide: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Slides'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']]
];
?>
<div class="slide-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
