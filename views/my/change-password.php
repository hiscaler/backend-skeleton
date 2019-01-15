<?php

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
$this->title = '修改密码';
$session = Yii::$app->getSession();
?>
<?php
if ($session->hasFlash('notice')):
    ?>
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">提示信息</h2>
            <p class="weui-msg__desc">
                <?= $session->getFlash('notice') ?>
            </p>
        </div>
    </div>
<?php
else:
    $form = ActiveForm::begin();
    $textInputOptions = [
        'template' => '<div class="weui-cell__hd"><label class="weui-label">{label}</label></div><div class="weui-cell__bd">{input}</div>',
        'options' => [
            'class' => 'weui-cell'
        ],
        'inputOptions' => [
            'class' => 'weui-input'
        ]
    ];
    ?>
    <div class="weui-cells weui-cells_form weui-cells__no-spacing">
        <?= $form->field($model, 'username', $textInputOptions)->textInput([
            'maxlength' => true,
            'readonly' => 'readonly',
        ]) ?>

        <?= $form->field($model, 'old_password', $textInputOptions)->passwordInput([
            'maxlength' => true,
            'placeholder' => '请输入旧密码',
        ]) ?>

        <?= $form->field($model, 'password', $textInputOptions)->passwordInput([
            'maxlength' => true,
            'placeholder' => '请输入新密码',
        ]) ?>

        <?= $form->field($model, 'confirm_password', $textInputOptions)->passwordInput([
            'maxlength' => true,
            'placeholder' => '请输入确认密码',
        ]) ?>
        <div class="weui-btn-area">
            <button type="submit" class="weui-btn weui-btn_primary">确定</button>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <div id="dialog-error" style="opacity: 1;<?= $model->hasErrors() ? '' : ' display: none' ?>">
        <div class="weui-mask"></div>
        <div class="weui-dialog weui-skin_android">
            <div class="weui-dialog__hd"><strong class="weui-dialog__title">错误提示</strong></div>
            <div class="weui-dialog__bd">
                <?= $form->errorSummary($model, [
                    'header' => '',
                ]) ?>
            </div>
            <div class="weui-dialog__ft">
                <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary" onclick="$('#dialog-error').hide()">关闭</a>
            </div>
        </div>
    </div>
<?php endif; ?>