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
        <div class="git-comments">
            <ol>
                <?php
                $formatter = Yii::$app->getFormatter();
                foreach ($gitComments as $i => $comment): ?>
                    <li>
                        <em><?= $formatter->asDatetime($comment['date']) ?></em>
                        <?= sprintf('%02d', $i + 1) ?>. <?= $comment['message'] ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</div>
<?php \app\modules\admin\components\CssBlock::begin() ?>
<style type="text/css">
    .blocks {
    }

    .blocks .left,
    .blocks .right {
        display: block;
        width: 50%;
        float: left;
    }

    .git-comments {
        padding: 0 10px;
    }

    .git-comments li {
        line-height: 24px;
    }

    .git-comments li em {
        margin-right: 10px;
        color: #ccc;
        float: right;
    }
</style>
<?php \app\modules\admin\components\CssBlock::end() ?>

