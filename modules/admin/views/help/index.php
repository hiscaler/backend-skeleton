<?php

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => '帮助中心', 'url' => ['help/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $body ?>
<?php $this->registerCssFile(Yii::$app->getRequest()->getBaseUrl() . '/admin/css/help.css') ?>
