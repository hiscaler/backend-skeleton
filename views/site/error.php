<?php
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */

/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="weui-msg">
    <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
    <div class="weui-msg__text-area">
        <h2 class="weui-msg__title"><?= $this->title ?></h2>
        <p class="weui-msg__desc">
            <?= nl2br(Html::encode($message)) ?>
        </p>
    </div>
    <div class="weui-msg__opr-area">
        <p class="weui-btn-area">
            <a href="<?= \yii\helpers\Url::toRoute(['/site/index']) ?>" class="weui-btn weui-btn_primary">·µ»ØÊ×Ò³</a>
        </p>
    </div>
</div>
