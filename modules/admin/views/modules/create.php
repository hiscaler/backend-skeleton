<?php


/* @var $this yii\web\View */
/* @var $model app\models\Module */

$this->title = Yii::t('app', 'Create Module');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']]
];
?>
<div class="module-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
