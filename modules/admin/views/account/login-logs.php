<?php

use yii\helpers\Html;

$formatter = Yii::$app->getFormatter();

$this->params['breadcrumbs'][] = Yii::t('app', 'Login Logs');
?>
<div class="widget-user-login-logs">
    <div class="bd">
        <ul class="time-lines">
            <?php
            foreach ($items as $key => $logs):
                echo Html::tag('li', $key, ['class' => 'day']);
                foreach ($logs as $log):
                    ?>
                    <li class="item">
                        <?= $formatter->asTime($log['login_at']) ?><em class="ip">[ <?= $log['login_ip'] ?> ]</em><em class="client-information"><?= $log['client_information'] ?></em>
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
