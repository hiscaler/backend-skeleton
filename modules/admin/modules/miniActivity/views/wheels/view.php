<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\Wheel */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '大转盘', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]]
];
?>
<div class="wheel-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'win_message',
            'get_award_message',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'description:ntext',
            'photo:image',
            'repeat_play_message',
            'background_image:image',
            'background_image_repeat_type',
            'finished_title',
            'finished_description:ntext',
            'finished_photo:image',
            'estimated_people_count',
            'actual_people_count',
            'play_times_per_person',
            'play_limit_type',
            'play_times_per_person_by_limit_type',
            'win_times_per_person',
            'win_interval_seconds',
            'show_awards_quantity:boolean',
            'blocks_count',
            'ordering',
            'enabled:boolean',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>
