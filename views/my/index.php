<?php
$this->title = '个人中心';
?>
<div class="weui-cells__title">个人资料</div>
<div class="weui-cells weui-cells__no-spacing">
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <p>帐号</p>
        </div>
        <div class="weui-cell__ft"><?= $identity->username ?></div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <p>注册时间</p>
        </div>
        <div class="weui-cell__ft"><?= date('Y-m-d', $identity->created_at) ?></div>
    </div>
</div>
<div class="weui-cells">
    <a class="weui-cell weui-cell_access" href="<?= \yii\helpers\Url::to(['/my/change-password']) ?>">
        <div class="weui-cell__bd">
            <p>修改密码</p>
        </div>
        <div class="weui-cell__ft"></div>
    </a>
</div>
<div class="weui-btn-area">
    <a href="<?= \yii\helpers\Url::to(['/site/logout']) ?>" class="weui-btn weui-btn_primary">退出</a>
</div>


