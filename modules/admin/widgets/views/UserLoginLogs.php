<?php

use yii\helpers\Html;

$formatter = Yii::$app->getFormatter();
?>
<div class="widget-user-login-logs">
    <div class="hd">登录日志</div>
    <div class="bd">
        <ul class="time-lines">
            <?php
            foreach ($items as $key => $item):
                echo Html::tag('li', $key, ['class' => 'day']);
                foreach ($item as $data):
                    ?>
                    <li class="item">
                        <?= $formatter->asTime($data['login_at']) ?><em class="ip">[ <?= $data['login_ip'] ?> ]</em><em class="client-information"><?= $data['client_information'] ?></em>
                    </li>
                    <?php
                endforeach;
            endforeach;
            ?>
        </ul>
    </div>
</div>