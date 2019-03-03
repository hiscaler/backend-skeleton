<?php

/* @var $this yii\web\View */

$this->title = '首页';
?>
<div class="blocks">
    <div class="left">
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
        ?>
    </div>
    <div class="right">
        <div class="system-information">
            <ul>
                <li><span>开发人员</span>hiscaler</li>
                <li><span>联系方式</span><a href="mailto:hiscaler@gmail.com">hiscaler@gmail.com</a></li>
                <li><span>当前版本</span><?= \app\models\Yad::getVersion() ?></li>
            </ul>
        </div>
    </div>
</div>

