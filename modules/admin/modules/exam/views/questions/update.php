<?php
/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\exam\models\Question */

$this->title = '更新: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '题目管理', 'url' => ['index', 'questionBankId' => $model->question_bank_id]];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="question-update">
    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>
</div>
