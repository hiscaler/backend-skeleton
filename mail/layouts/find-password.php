<?php

use yii\helpers\Html;

$this->context->layout = false;
/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body style="margin:0; padding:0;  font-family:微软雅黑; font-size:16px; line-height:22px; color:#505050;">
        <?php $this->beginBody() ?>
        <div class="message"><?= $content ?></div>
        <div class="url"><?= $url ?></div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
