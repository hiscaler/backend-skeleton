<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\exam\models\QuestionBank */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '题库管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => '列表', 'url' => ['index']],
    ['label' => '添加', 'url' => ['create']],
    ['label' => '更新', 'url' => ['update', 'id' => $model->id]],
];
?>
<div class="question-bank-view">
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description',
            'icon:image',
            'questions_count',
            'participation_times',
            'status:questionBankStatus',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ])
    ?>
</div>
