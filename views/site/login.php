<?php
/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

AppAsset::register($this);

$baseUrl = Yii::$app->getRequest()->getBaseUrl();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <?= Html::csrfMetaTags() ?>
    <title>µÇÂ¼ - <?= Yii::$app->name ?></title>
    <style type="text/css">
        body {
            background-color: #ffffff;
            padding: 0 10px;
        }

        input:-webkit-autofill {
            border: 1px solid #ffffff;
            -webkit-box-shadow: inset 0 0 0px 9999px #ffffff;
        }

        input:focus,
        input:-webkit-autofill:focus {
            border-color: #ffffff;
        }

        #container {
            background-color: #ffffff;
        }

        .page-login {
            margin: 20% auto 0 auto;
        }

        .page-login .hd {
        }

        .page-login .hd .logo {
            display: block;
            margin: 0 auto;
            font-size: 30px;
            padding-left: 20px;
            margin-bottom: 20px;
        }

        .page-login .bd {
            padding: 0 15px;
        }

        .page-login .help-block {
            font-size: 0.8rem;
            color: #CE3C39
        }

        .page-login .weui-cells {
            position: inherit;
            margin-top: 1em;
            height: 130px;
            background-color: #ffffff;
        }

        .page-login .weui-cell {
            margin: 10px;
            position: inherit;
            border-bottom: 1px solid #dedede !important;
        }

        .page-login .weui-cell .weui-label {
            width: 40px;
            font-size: 16px;
            font-weight: normal;
            color: #000;
        }

        .page-login .weui-cell__bd {
            padding-left: 15px;
        }

        .page-login .weui-cell__bd input {
            font-size: 14px;
            height: 30px;
            line-height: 30px;
            padding-left: 10px;
        }

        .page-login .weui-btn-area {
            margin-left: 0;
            margin-right: 0;
        }
    </style>
    <?php $this->head() ?>
</head>
<body ontouchstart style="background-color: #FFF">
<?php $this->beginBody() ?>
<div class="container" id="container">
    <div class="page-login">
        <div class="hd">
            <div class="logo">ÏµÍ³µÇÂ¼</div>
        </div>
        <div class="bd">
            <?php
            $form = ActiveForm::begin([
                'id' => 'login-form',
                'class' => 'm-t',
                'errorCssClass' => 'weui-cell_warn'
            ]);
            $options = [
                'template' => '<div class="weui-cell__hd"><label class="weui-label">{label}</label></div><div class="weui-cell__bd">{input}</div><div class="weui-cell__ft"><i class="weui-icon-warn"></i></div>',
                'options' => [
                    'class' => 'weui-cell'
                ],
                'inputOptions' => [
                    'class' => 'weui-input'
                ]
            ];
            ?>
            <div class="weui-cells weui-cells_form">
                <?= $form->field($model, 'username', $options)->textInput(['class' => 'weui-input', 'placeholder' => 'ÇëÌîÐ´µÇÂ¼ÕËºÅ']) ?>

                <?= $form->field($model, 'password', $options)->passwordInput(['class' => 'weui-input', 'placeholder' => 'ÇëÌîÐ´µÇÂ¼ÃÜÂë']) ?>
            </div>
            <div class="weui-btn-area">
                <?= Html::submitButton('µÇÂ¼', ['class' => 'weui-btn weui-btn_primary weui-btn_disabled']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
<script type="text/javascript">
    $(function () {
        $('body').css({'backgroundSize': '100% ' + $(document.body).height() + 'px'});
        if ($('#loginform-username').val() !== '' && $('#loginform-password').val() !== '') {
            $('.weui-btn-area button').removeClass('weui-btn_disabled');
        }
        $('#loginform-username, #loginform-password').change(function () {
            if ($(this).val() !== '') {
                $('.weui-btn-area button').removeClass('weui-btn_disabled');
            } else {
                $('.weui-btn-area button').addClass('weui-btn_disabled');
            }
        });
    });
</script>
<?= \app\widgets\WechatShare::widget() ?>
</body>
</html>
<?php $this->endPage() ?>
<body>
