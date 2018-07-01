<?php
$this->title = Yii::t('site', 'Member sign in');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container white-bg">
    <div class="form-signup">
        <div class="title"><?= Yii::t('site', 'Member sign in') ?></div>

        <?php $form = \yii\widgets\ActiveForm::begin(); ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= \yii\helpers\Html::submitButton(Yii::t('site', 'Sign in now'), ['class' => 'btn-signup']) ?>
        </div>

        <?php \yii\widgets\ActiveForm::end(); ?>

        <p>
            <a href="<?= \yii\helpers\Url::toRoute(['site/signup']) ?>"><?= Yii::t('site', 'New ID register') ?></a>|
            <a href="<?= \yii\helpers\Url::toRoute(['site/forget-password']) ?>"><?= Yii::t('site', 'Forget password?') ?></a>
        </p>
    </div>
</div>
