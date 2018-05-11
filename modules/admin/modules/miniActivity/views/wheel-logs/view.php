<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\miniActivity\models\WheelLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Wheel Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wheel-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'wheel_id',
            'is_win',
            'award_id',
            'ip_address',
            'post_datetime:datetime',
            'member_id',
            'is_get',
            'get_password',
            'get_datetime:datetime',
            'remark:ntext',
        ],
    ]) ?>

</div>
