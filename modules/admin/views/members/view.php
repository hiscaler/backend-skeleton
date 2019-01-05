<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Member */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]],
];

$wechatModel = $model->wechat;
$profile = $model->profile;
?>
<div>
    <ul class="tabs-common">
        <li class="active"><a href="javascript:;" data-toggle="tab-panel-basic">基本资料</a></li>
        <?php if ($profile): ?>
            <li><a href="javascript:;" data-toggle="tab-panel-profile">扩展资料</a></li>
        <?php endif; ?>
        <?php if ($wechatModel): ?>
            <li><a href="javascript:;" data-toggle="tab-panel-wehcat">微信资料</a></li>
        <?php endif; ?>
    </ul>
    <div class="panels">
        <div class="tab-panel" id="tab-panel-basic">
            <div class="member-view">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'type:memberType',
                        'parent.username',
                        'username',
                        'nickname',
                        'real_name',
                        [
                            'attribute' => 'avatar',
                            'format' => 'image',
                            'contentOptions' => ['class' => 'avatar']
                        ],
                        'email:email',
                        'mobile_phone',
                        [
                            'attribute' => 'register_ip',
                            'value' => function ($model) {
                                return $model['register_ip'];
                            },
                        ],
                        'total_credits',
                        'available_credits',
                        'login_count',
                        'last_login_ip',
                        'last_login_time:datetime',
                        'status:memberStatus',
                        'expired_datetime:datetime',
                        'remark:ntext',
                        'created_at:datetime',
                        'updated_at:datetime',
                    ],
                ]) ?>
            </div>
        </div>
        <?php if ($profile): ?>
            <div class="tab-panel" id="tab-panel-profile" style="display: none;">
                <?= DetailView::widget([
                    'model' => $wechatModel,
                    'attributes' => [
                        'tel',
                        'address',
                        'zip_code',
                    ],
                ]) ?>
            </div>
        <?php endif; ?>
        <?php if ($wechatModel): ?>
            <div class="tab-panel" id="tab-panel-wehcat" style="display: none;">
                <?= DetailView::widget([
                    'model' => $wechatModel,
                    'attributes' => [
                        'id',
                        'subscribe:boolean',
                        'openid',
                        'subscribe:boolean',
                        'nickname',
                        'sex:sex',
                        'country',
                        'province',
                        'city',
                        'language',
                        [
                            'attribute' => 'headimgurl',
                            'format' => 'image',
                            'contentOptions' => ['class' => 'avatar']
                        ],
                        'subscribe_time:datetime',
                        'unionid',
                    ],
                ]) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

