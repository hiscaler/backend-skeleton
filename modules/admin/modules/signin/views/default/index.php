<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\signin\models\SigninSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Signins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="signin-index">
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'member_id',
            'ymd',
            'signin_datetime:datetime',
            'ip_address',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
