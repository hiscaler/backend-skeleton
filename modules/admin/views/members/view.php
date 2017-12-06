<?php

use yii\grid\GridView;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Update'), 'url' => ['update', 'id' => $model->id]],
];
?>

<ul class="tabs-common clearfix">
    <li class="active"><a href="javascript:;" data-toggle="panel-base">基本资料</a></li>
    <li><a href="javascript:;" data-toggle="panel-meta">扩展信息</a></li>
    <li><a href="javascript:;" data-toggle="panel-credits">积分情况</a></li>
    <li><a href="javascript:;" data-toggle="panel-login-logs">登录日志</a></li>
</ul>

<div class="panels">
    <div id="panel-base" class="tab-pane">
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'username',
                'nickname',
                'avatar:image',
                'credits_count',
                'user_group_text',
                'register_ip',
                'login_count',
                'last_login_ip',
                'last_login_time',
                'status_text',
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ])
        ?>
    </div>

    <!-- 扩展信息 -->
    <div id="panel-meta" class="tab-pane" style="display: none;">
        <div class="grid-view">
            <table class="table">
                <thead>
                <tr>
                    <th>标题</th>
                    <th>内容</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $items = app\models\Meta::getItems($model);
                foreach ($items as $item):
                    ?>
                    <tr>
                        <td style="width: 160px;"><?= $item['label'] ?></td>
                        <td>
                            <?php
                            $value = $item['value'];
                            if (is_array($value) || $item['input_candidate_value']) {
                                $output = [];
                                $value = is_array($value) ? $value : [$value];
                                foreach ($value as $v) {
                                    if (isset($item['input_candidate_value'][$v])) {
                                        $output[] = $item['input_candidate_value'][$v];
                                    } else {
                                        $output[] = $v;
                                    }
                                }
                                $value = implode('、', $output);
                            }

                            echo $value;
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- // 扩展信息 -->

    <div id="panel-credits" class="tab-pane" style="display: none;">
        <?php
        echo GridView::widget([
            'dataProvider' => $creditLogsDataProvider,
            'caption' => yii\helpers\Html::a('+', ['add-credits', 'id' => $model['id']], ['class' => 'btn-circle']),
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['class' => 'serial-number']
                ],
                [
                    'attribute' => 'operation_formatted',
                    'contentOptions' => ['style' => 'width: 100px;', 'class' => 'center']
                ],
                [
                    'attribute' => 'credits',
                    'contentOptions' => ['class' => 'number']
                ],
                [
                    'attribute' => 'remark',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'datetime',
                    'contentOptions' => ['class' => 'datetime'],
                ],
                [
                    'attribute' => 'creater.username',
                    'headerOptions' => ['class' => 'last'],
                    'contentOptions' => ['class' => 'username'],
                ],
            ],
        ]);
        ?>
    </div>

    <div id="panel-login-logs" class="tab-pane" style="display: none;">
        登录日志
    </div>
</div>