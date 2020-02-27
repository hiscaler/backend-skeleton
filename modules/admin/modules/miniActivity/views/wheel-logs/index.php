<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\miniActivity\models\WheelLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '大转盘记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wheel-log-index">
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'is_win',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean']
            ],
            'award_id',
            'ip_address',
            //'post_datetime:datetime',
            //'member_id',
            //'is_get',
            //'get_password',
            //'get_datetime:datetime',
            //'remark:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
