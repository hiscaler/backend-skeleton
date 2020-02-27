<?php

use app\helpers\Config;
use app\models\Lookup;
use yii\captcha\Captcha;
use yii\widgets\ActiveForm;

$name = Lookup::getValue('custom.site.name') ?: Yii::$app->name;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <meta name="robots" content="none" />
    <meta HTTP-EQUIV="pragma" CONTENT="no-cache" />
    <meta name="language" content="en" />
    <link rel="stylesheet" type="text/css" href="<?= Yii::$app->getRequest()->getBaseUrl() . '/admin/css/login.css' ?>" />
    <title>后台管理登录 - <?= $name ?></title>
</head>
<body>
<div id="logo">
    <?= $name ?>后台管理登录
</div>
<div id="in">
    <div id="left"></div>
    <div id="right">
        <?php
        $fieldConfigs = [
            'options' => ['class' => 'entry', 'tag' => 'li'],
            'template' => '{label}{input}{hint}<div class="clearfix">{error}</a>',
        ];
        $form = ActiveForm::begin([
            'id' => 'login-form',
            'enableAjaxValidation' => false,
            'options' => [
                'class' => Config::get('identity.hideCaptcha') ? 'no-verify-code' : '',
            ]
        ]);
        ?>
        <ul>
            <?= $form->field($model, 'username', $fieldConfigs)->textInput(['tabindex' => 1]); ?>

            <?= $form->field($model, 'password', $fieldConfigs)->passwordInput(); ?>

            <?php
            if (Config::get('identity.hideCaptcha') === false) {
                echo $form->field($model, 'verify_code', $fieldConfigs)->widget(Captcha::class, [
                    'template' => '{input}{image}',
                    'captchaAction' => 'default/captcha',
                ]);
            }
            ?>
            <li>
                <input type="submit" name="bt_login" id="bt_login" value="" class="button" />
                <?= $form->field($model, 'remember_me')->checkbox(); ?>
            </li>
        </ul>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div id="footer">
    Copyright &copy; <?= date('Y'); ?> by <?= $name; ?> All Rights Reserved.
</div>
</body>
</html>
