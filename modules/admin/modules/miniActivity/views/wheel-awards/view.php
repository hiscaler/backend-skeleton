<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\WheelAward */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '奖品设置', 'url' => ['index', 'wheelId' => $model['wheel_id']]];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index', 'wheelId' => $model['wheel_id']]],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create', 'wheelId' => $model['wheel_id']]],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]]
];
?>
<div class="wheel-award-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'ordering',
            'title',
            'description:ntext',
            'photo:image',
            'total_quantity',
            'remaining_quantity',
            'enabled:boolean',
        ],
    ]) ?>
</div>
