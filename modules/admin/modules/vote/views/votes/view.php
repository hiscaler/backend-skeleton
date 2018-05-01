<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\vote\models\Vote */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '投票管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]]
];
?>
<div class="vote-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category.name',
            'title',
            'description:ntext',
            'begin_datetime:datetime',
            'end_datetime:datetime',
            'total_votes_count',
            'allow_anonymous:boolean',
            'allow_view_results:boolean',
            'allow_multiple_choice:boolean',
            'interval_seconds',
            'items:ntext',
            'voting_result:raw',
            'ordering',
            'enabled:boolean',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ],
    ]) ?>
</div>
<?php \app\modules\admin\components\CssBlock::begin() ?>
<style type="text/css">
    /*******************************************************************************
 * Vote
 ******************************************************************************/
    .vote-friendly-result {
        padding: 4px;
        margin-top: 0px !important;
    }

    .vote-friendly-result dt {
        line-height: 28px;
        font-size: 16px;
        text-indent: 0px;
        margin: 0 0 15px 0
    }

    .vote-friendly-result dd {
        color: #6c6c6c;
        font-size: 14px;
        line-height: 26px;
        vertical-align: middle;
    }

    /* Chart */
    .vote-friendly-result li {
        line-height: 18px;
    }

    .vote-friendly-result li .option {
        padding: 8px 0;
    }

    .vote-friendly-result .option, .vote-friendly-result .vote-friendly-result, .item .counter {
        float: left;
        display: inline;
    }

    .vote-friendly-result .option {
        width: 280px;
    }

    .vote-friendly-result .bars {
        width: 220px;
        margin-left: 20px;
        float: left;
    }

    .vote-friendly-result .bar {
        width: 150px;
        height: 14px;
        overflow: hidden;
        background-color: #e3e3e3;
        float: left;
        display: inline;
        margin: 10px 0;
        margin-right: 10px;
    }

    .vote-friendly-result .precent {
        background-color: #0192ad;
        border-right: 1px solid #fff;
        height: 14px;
        width: 0%;
    }

    .vote-friendly-result .data {
        height: 34px;
        line-height: 34px;
        float: right;
    }

    .vote-friendly-result .counter {
        padding: 0 4px;
        background-color: #F5F5F5;
        border: #CCC solid 1px;
        border-radius: 5px;
        margin-top: 6px;
        text-align: right;
        margin-left: 10px;
        float: left;
        font-weight: bold;
    }
</style>
<?php \app\modules\admin\components\CssBlock::end() ?>
