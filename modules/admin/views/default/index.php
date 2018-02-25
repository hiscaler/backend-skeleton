<?php

/* @var $this yii\web\View */

$this->title = '首页';
?>
<?php
$dependency = [
    'class' => 'yii\caching\DbDependency',
    'sql' => 'SELECT [[last_login_time]] FROM {{%user}} WHERE [[id]] = :id',
    'params' => [':id' => Yii::$app->getUser()->getId()]
];
if ($this->beginCache(\app\modules\admin\widgets\UserLoginLogs::class, ['dependency' => $dependency])) {
    echo \app\modules\admin\widgets\UserLoginLogs::widget();
    $this->endCache();
}