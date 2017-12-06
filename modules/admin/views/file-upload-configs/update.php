<?php
/* @var $this yii\web\View */
/* @var $model app\models\FileUploadConfig */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'File Upload Config',
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'File Upload Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->model_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'View'), 'url' => ['view', 'id' => $model->id]],
];
?>
<div class="upload-config-update">

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
