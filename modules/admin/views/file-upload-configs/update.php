<?php
/* @var $this yii\web\View */
/* @var $model app\models\FileUploadConfig */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
        'modelClass' => Yii::t('model', 'File Upload Config'),
    ]) . $model->attribute;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'File Upload Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update') . " {$model->attribute}";

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];
?>
<div class="upload-config-update">
    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>
</div>
