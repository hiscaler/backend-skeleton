<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
    <meta name="robots" content="none"/>
    <meta HTTP-EQUIV="pragma" CONTENT="no-cache"/>
    <meta name="language" content="en"/>
    <link rel="stylesheet" type="text/css" href="<?= Yii::$app->getRequest()->baseUrl . '/admin/css/login.css' ?>"/>
    <title><?php echo Html::encode($this->title); ?></title>
</head>
<body>
<div id="logo">
    后台管理系统
</div>
<div id="in">
    <div id="left"></div>
    <div id="right">
        <?php
        $fieldConfigs = [
            'options' => ['class' => 'entry', 'tag' => 'li',],
            'template' => '{label}{input}{hint}<div class="clearfix">{error}</a>',
        ];
        $form = ActiveForm::begin([
            'id' => 'login-form',
            'enableAjaxValidation' => false,
        ]);
        ?>
        <ul>
            <?= $form->field($model, 'username', $fieldConfigs)->textInput(['tabindex' => 1]); ?>

            <?= $form->field($model, 'password', $fieldConfigs)->passwordInput(); ?>

            <?=
            $form->field($model, 'verifyCode', $fieldConfigs)->widget(Captcha::className(), [
                'template' => '{input}{image}',
                'captchaAction' => 'default/captcha',
            ]);
            ?>
            <li>
                <?= $form->field($model, 'rememberMe')->checkbox(); ?>
            </li>
            <li><input type="submit" name="bt_login" id="bt_login" value="" class="button"/></li>
        </ul>

        <?php ActiveForm::end(); ?>
    </div>

</div>
<div id="footer">
    Copyright &copy; <?= date('Y'); ?> by <?= Yii::$app->name; ?> All Rights Reserved.
</div>
</body>
</html>
