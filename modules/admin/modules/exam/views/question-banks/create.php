<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\exam\models\QuestionBank */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '题库管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => '列表', 'url' => ['index']],
    ['label' => '更新', 'url' => ['update', 'id' => $model->id]],
    ['label' => '详情', 'url' => ['view', 'id' => $model->id]],
];
?>
<div class="question-bank-create">
    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>
</div>
