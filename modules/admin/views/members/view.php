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

/* @var $formatter \app\modules\admin\extensions\Formatter */
$formatter = Yii::$app->getFormatter();
$wechatModel = $model->wechat;
$profile = $model->profile;
$creditLogs = $model->creditLogs;
$loginLogs = $model->loginLogs;
?>
<div>
    <ul class="tabs-common">
        <li class="active"><a href="javascript:;" data-toggle="tab-panel-basic">基本资料</a></li>
        <?php if ($profile): ?>
            <li><a href="javascript:;" data-toggle="tab-panel-profile">附加资料</a></li>
        <?php endif; ?>
        <?php if ($metaItems): ?>
            <li><a href="javascript:;" data-toggle="tab-panel-meta">扩展资料</a></li>
        <?php endif; ?>
        <?php if ($wechatModel): ?>
            <li><a href="javascript:;" data-toggle="tab-panel-wehcat">微信资料</a></li>
        <?php endif; ?>
        <?php if ($creditLogs): ?>
            <li><a href="javascript:;" data-toggle="tab-panel-credit-logs">积分日志</a></li>
        <?php endif; ?>
        <?php if ($loginLogs): ?>
            <li><a href="javascript:;" data-toggle="tab-panel-login-logs">登录日志</a></li>
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
                        'role:memberRole',
                        'usable_scope:memberUsableScope',
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
                        'total_money:yuan',
                        'available_money:yuan',
                        'total_credits',
                        'available_credits',
                        [
                            'attribute' => 'alarm_credits',
                            'value' => function ($model) {
                                return $model['alarm_credits'] ?: null;
                            },
                        ],
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
                    'model' => $profile,
                    'attributes' => [
                        'tel',
                        'address',
                        'zip_code',
                    ],
                ]) ?>
            </div>
        <?php endif; ?>
        <?php if ($metaItems): ?>
            <div class="tab-panel" id="tab-panel-meta" style="display: none;">
                <div class="grid-view">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>标签</th>
                            <th class="last">值</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        foreach ($metaItems as $item):
                            $i++;
                            ?>
                            <tr>
                                <td class="serial-number"><?= $i ?></td>
                                <td style="width: 120px;"><?= $item['label'] ?></td>
                                <td><?= $item['value'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
        <?php if ($creditLogs): ?>
            <div class="tab-panel" id="tab-panel-credit-logs" style="display: none;">
                <div class="grid-view">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>动作</th>
                            <th>外部关联数据</th>
                            <th>积分</th>
                            <th>备注</th>
                            <th class="last">操作时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($creditLogs as $i => $log): ?>
                            <tr>
                                <td class="serial-number"><?= $i + 1 ?></td>
                                <td style="width: 120px;"><?= $formatter->asMemberCreditOperation($log['operation']) ?></td>
                                <td style="width: 60px;"><?= $log['related_key'] ?></td>
                                <td class="number"><?= $log['credits'] ?></td>
                                <td><?= $log['remark'] ?></td>
                                <td class="datetime"><?= date('Y-m-d H:i:s', $log['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($loginLogs): ?>
            <div class="tab-panel" id="tab-panel-login-logs" style="display: none;">
                <div class="grid-view">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>登录时间</th>
                            <th>IP</th>
                            <th class="last">客户端信息</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($loginLogs as $i => $log): ?>
                            <tr>
                                <td class="serial-number"><?= $i + 1 ?></td>
                                <td class="datetime"><?= $formatter->asDatetime($log['login_at']) ?></td>
                                <td class="ip-address"><?= $log['ip'] ?></td>
                                <td><?= $log['client_information'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

