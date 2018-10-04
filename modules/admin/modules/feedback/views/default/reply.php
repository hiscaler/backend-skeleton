<?php

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\feedback\models\Feedback */

$this->title = $feedback->title ?: $model->id;
$this->params['breadcrumbs'][] = ['label' => '留言反馈管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="form-outside">
    <div class="form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'message')->textarea(['rows' => 12]) ?>
        <div class="form-group buttons">
            <?= \yii\helpers\Html::submitButton('回复', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
