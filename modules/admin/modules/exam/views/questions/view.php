<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\exam\models\Question */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '题目管理', 'url' => ['index', 'questionBankId' => $model->question_bank_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="question-view">
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'bank.name',
            'type:questionType',
            'status:questionStatus',
            'content:ntext',
            'options:ntext',
            'answer:ntext',
            'resolve:ntext',
        ],
    ])
    ?>
</div>
