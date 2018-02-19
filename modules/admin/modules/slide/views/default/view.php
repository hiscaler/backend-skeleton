<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Slide */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('slide.model', 'Slides'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]],
];
?>
<div class="slide-view">
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'ordering',
            [
                'attribute' => 'category.name',
                'visible' => $model['category_id'],
            ],
            'title',
            'url:url',
            'url_open_target_text',
            [
                'attribute' => 'picture_path',
                'format' => 'raw',
                'value' => function ($model) {
                    $picturePath = $model->picture_path;

                    return empty($picturePath) ? null : \yii\helpers\Html::img($picturePath, ['class' => 'thumbnail']);
                }
            ],
            'enabled:boolean',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ])
    ?>
</div>
