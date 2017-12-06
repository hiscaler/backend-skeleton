<?php
$this->title = Yii::t('site', 'Find Password');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container white-bg">
    <div class="form-signup">

        <div class="title"><?= Yii::t('site', 'Find Password') ?></div>
        
        <?php $form = \yii\widgets\ActiveForm::begin(); ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= \yii\helpers\Html::submitButton(Yii::t('site', 'Save'), ['class' => 'btn-signup']) ?>
        </div>

        <?php \yii\widgets\ActiveForm::end(); ?>
    </div>
</div>
