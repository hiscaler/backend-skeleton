<?php
/* @var $this yii\web\View */
/* @var $model app\models\FileUploadConfig */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'File Upload Config',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'File Upload Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="file-upload-config-create">

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
