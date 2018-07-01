<?php
$this->title = Yii::t('site', 'Register');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container white-bg">
    <?php if (isset($next) && $next == 'message'): ?>

        <div class="widget-message">
            <div class="message">
                <p>您的注册信息已提交成功。</p>
                <p>我们会在3个工作日内审核您的注册信息。</p>
            </div>
        </div>

    <?php else: ?>
        <div class="form-signup form-signin">
            <div class="title"><?= Yii::t('site', 'Register') ?></div>

            <?php $form = \yii\widgets\ActiveForm::begin(); ?>

            <?= $form->field($model, 'username')->textInput(['maxlength' => true])->hint(Yii::t('site', '6-12 numbers or letter combination a-z')) ?>

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->hint(Yii::t('site', '6-12 numbers or letter combination a-z')) ?>

            <?= $form->field($model, 'confirm_password')->passwordInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true])->hint('') ?>

            <!-- 会员资料 -->
            <?php
            foreach ($metaItems as $key => $item) {
                echo $form->field($dynamicModel, $key)->{$item['input_type']}(['value' => ''])->label($item['label'])->hint(Yii::t('site', 'Optional'));
            }
            ?>
            <!-- // 会员资料 -->

            <div class="form-group">
                <?= \yii\helpers\Html::submitButton(Yii::t('site', 'Save'), ['class' => 'btn-signup']) ?>
            </div>

            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    <?php endif; ?>

</div>
