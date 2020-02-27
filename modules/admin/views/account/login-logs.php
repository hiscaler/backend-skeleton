<?php

use yii\helpers\Html;

$formatter = Yii::$app->getFormatter();

$this->title = Yii::t('app', 'Login Logs');
$this->params['breadcrumbs'][] = ['label' => '帐户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="widget-member-login-logs">
    <div class="bd">
        <ul class="time-lines">
            <?php
            foreach ($items as $key => $logs):
                echo Html::tag('li', $key, ['class' => 'day']);
                foreach ($logs as $log):
                    ?>
                    <li class="item">
                        <?= $formatter->asTime($log['login_at']) ?><em class="ip">[ <?= $log['ip'] ?> ]</em><em class="client-information"><?= $log['client_information'] ?></em>
                    </li>
                <?php
                endforeach;
            endforeach;
            ?>
        </ul>
    </div>
</div>
<?= \yii\widgets\LinkPager::widget([
    'pagination' => $pagination,
]) ?>
