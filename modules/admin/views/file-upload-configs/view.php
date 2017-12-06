<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\FileUploadConfig */

$this->title = $model->model_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'File Upload Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->model_name;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]],
];
?>
<div class="upload-config-view">

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'type_text',
//            'model_name:modelName',
            'attribute',
            'extensions',
            [
                'label' => Yii::t('fileUploadConfig', 'Size'),
                'value' => $model['min_size'] . 'KB ~ ' . $model['max_size'] . 'KB',
                'visible' => $model['min_size'] && $model['max_size']
            ],
            [
                'label' => Yii::t('fileUploadConfig', 'Thumb'),
                'value' => $model['thumb_width'] . 'PX - ' . $model['thumb_height'] . 'PX',
                'visible' => $model['thumb_width'] && $model['thumb_height']
            ],
            [
                'attribute' => 'created_by',
                'value' => $model['creater']['nickname']
            ],
            'created_at:datetime',
            [
                'attribute' => 'updated_by',
                'value' => $model['updater']['nickname']
            ],
            'updated_at:datetime',
            [
                'attribute' => 'deleted_by',
                'value' => $model['deleter']['nickname']
            ],
        ],
    ])
    ?>

</div>
