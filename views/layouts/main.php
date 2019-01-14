<?php
/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title><?= $this->title ?> - <?= Yii::$app->name ?></title>
    <?php $this->head() ?>
</head>
<body ontouchstart>
<?php $this->beginBody() ?>
<div class="container">
    <div class="page">
        <div class="page__hd">
            <div id="banner"></div>
        </div>
        <div class="page__bd">
            <?= $content ?>
        </div>
        <div class="page__ft">
            <?= app\widgets\Tabbar::widget() ?>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
