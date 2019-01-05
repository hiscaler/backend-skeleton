<?php
$this->context->layout = false;
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="google" value="notranslate">
    <!--[if lte IE 9]>
    <meta http-equiv="refresh" content="0;url=/browser">
    <![endif]-->
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?= Yii::$app->getRequest()->getBaseUrl() . '/css/help.css' ?>">
    <title>帮助文档</title>
</head>
<body>
<header id="header" class="wrapper">
    <div id="header-inner" class="inner">
        <h1 id="logo-wrap">
            <a href="<?= \yii\helpers\Url::toRoute(['help/index']) ?>" id="logo">帮助文档</a>
        </h1>
        <nav id="main-nav">
            <a href="<?= \yii\helpers\Url::toRoute(['help/index', 'type' => 'dict']) ?>" class="main-nav-link<?= $type == 'dict' ? ' main-nav-link-active' : '' ?>">数据词典</a>
            <a href="<?= \yii\helpers\Url::toRoute(['help/index', 'type' => 'api']) ?>" class="main-nav-link<?= $type == 'api' ? ' main-nav-link-active' : '' ?>">接口文档</a>
            <a target="_blank" href="<?= \yii\helpers\Url::toRoute(['/api/default/index']) ?>" class="main-nav-link">API</a>
        </nav>
    </div>
</header>
<div id="container">
    <div id="content-wrap">
        <div id="content" class="wrapper">
            <div id="content-inner">
                <article class="article-container">
                    <div class="article-inner">
                        <div class="article">
                            <div class="article-content">
                                <?= $article ?>
                            </div>
                        </div>
                    </div>
                </article>
                <aside id="sidebar" role="navigation">
                    <div class="inner">
                        <strong class="sidebar-title">目录导航</strong>
                        <?php foreach ($sections as $key => $title): ?>
                            <a class="sidebar-link<?= $key == $file ? ' current' : '' ?>" href="<?= \yii\helpers\Url::toRoute(['help/index', 'type' => $type, 'file' => $key]) ?>"><?= $title ?></a>
                        <?php endforeach; ?>
                    </div>
                </aside>
            </div>
        </div>
    </div>
    <footer id="footer" class="wrapper">
        <div class="inner">
            <div id="footer-copyright">
                © <?= date('Y') . ' ' . Yii::$app->name ?>
            </div>
        </div>
    </footer>
</div>
<?php $this->endBody() ?>
</body>
</html>