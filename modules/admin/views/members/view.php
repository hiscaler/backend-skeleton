<?php

use yii\helpers\Html;
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
?>
<div>
    <ul class="tabs-common">
        <li class="active"><a href="javascript:;" data-toggle="tab-panel-basic">基本资料</a></li>
        <li><a href="javascript:;" data-toggle="tab-panel-wehcat">微信资料</a></li>
    </ul>
    <div class="panels">
        <div class="tab-panel" id="tab-panel-basic">
            <div class="member-view">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
//                        'type',
                        'username',
                        'nickname',
                        [
                            'attribute' => 'avatar',
                            'format' => 'image',
                            'contentOptions' => ['class' => 'avatar']
                        ],
                        'email:email',
                        'tel',
                        'mobile_phone',
                        [
                            'attribute' => 'register_ip',
                            'value' => function ($model) {
                                return long2ip($model['register_ip']);
                            },
                        ],
                        'login_count',
                        [
                            'attribute' => 'last_login_ip',
                            'value' => function ($model) {
                                return long2ip($model['last_login_ip']);
                            },
                        ],
                        'last_login_time:datetime',
                        'status:memberStatus',
                        'remark:ntext',
                        'created_at:datetime',
                        'updated_at:datetime',
                    ],
                ]) ?>
            </div>
        </div>
        <div class="tab-panel" id="tab-panel-wehcat" style="display: none;">
            <?= DetailView::widget([
                'model' => $model->wechat,
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
    </div>
</div>

