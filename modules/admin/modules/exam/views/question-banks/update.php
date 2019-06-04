<?php
/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\exam\models\QuestionBank */

$this->title = '更新: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '题库管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="question-bank-update">
    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>
</div>
