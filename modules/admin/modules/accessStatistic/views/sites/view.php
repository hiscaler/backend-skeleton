<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\accessStatistic\models\AccessStatisticSite */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Access Statistic Sites', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]]
];
?>
<div class="access-statistic-site-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'enabled:boolean',
            'created_at:datetime',
            'creater.nickname',
            'updated_at:datetime',
            'updater.nickname',
        ],
    ]) ?>
</div>
