<?php
$this->title = Yii::t('site', 'Reset Password');
$this->params['breadcrumbs'][] = $this->title;

$session = Yii::$app->getSession();
?>

<div class="container white-bg">

    <?php if ($session->hasFlash('notice')): ?>
        <div class="notice">
            <?= $session->getFlash('notice') ?>
        </div>
    <?php else: ?>
        <div class="form-signup">

            <div class="title"><?= Yii::t('site', 'Forget password') ?></div>

            <?php $form = \yii\widgets\ActiveForm::begin(); ?>

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'confirm_password')->passwordInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?= \yii\helpers\Html::submitButton(Yii::t('site', 'Save'), ['class' => 'btn-signup']) ?>
            </div>

            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    <?php endif; ?>
</div>
