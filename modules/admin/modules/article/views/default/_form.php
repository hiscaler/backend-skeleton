<?php

use yadjet\editor\UEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\article\models\Article */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="article-form form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'keyword')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

        <?=
        UEditor::widget([
            'form' => $form,
            'model' => $model,
            'attribute' => 'content',
        ])
        ?>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
