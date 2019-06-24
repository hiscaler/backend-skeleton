<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\ticket\models\Ticket */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '工单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => '回复消息', 'url' => ['messages/create', 'ticketId' => $model->id]],
];
$attachments = $model->attachments;
$messages = $model->messages;
?>
<div>
    <ul class="tabs-common">
        <li class="active"><a href="javascript:;" data-toggle="tab-panel-basic">基本资料</a></li>
        <li><a href="javascript:;" data-toggle="tab-panel-attachments">附件<span class="badges badges-red"><?= count($attachments) ?></span></a></li>
        <li><a href="javascript:;" data-toggle="tab-panel-messages">消息日志<span class="badges badges-red"><?= count($messages) ?></span></a></li>
    </ul>
    <div class="panels">
        <div class="tab-panel" id="tab-panel-basic">
            <div class="ticket-view">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'category.name',
                        'title',
                        'description:ntext',
                        'confidential_information:ntext',
                        'mobile_phone',
                        'email:email',
                        'status:ticketStatus',
                        'created_at:datetime',
                        'creater.nickname',
                        'updated_at:datetime',
//                        'updated_by',
                    ],
                ]) ?>
            </div>
        </div>
        <div class="tab-panel" id="tab-panel-attachments" style="display: none">
            <?php if ($attachments): ?>
                <div class="grid-view">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>附件</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($attachments as $i => $attachment): ?>
                            <tr>
                                <td class="serial-number"><?= $i + 1 ?></td>
                                <td><?= $attachment['path'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <?= \app\modules\admin\components\MessageBox::widget([
                    'message' => '暂无附件',
                ]) ?>
            <?php endif; ?>
        </div>
        <div class="tab-panel" id="tab-panel-messages" style="display: none">
            <?php if ($messages): ?>
                <div class="grid-view">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>内容</th>
                            <th>互动时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($messages as $i => $message): ?>
                            <tr>
                                <td class="serial-number"><?= $i + 1 ?></td>
                                <td><?= $message['content'] ?></td>
                                <td class="datetime"><?= date('Y-m-d H:i:s', $message['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <?= \app\modules\admin\components\MessageBox::widget([
                    'message' => '暂无互动消息   ' . \yii\helpers\Html::a('回复消息', ['messages/create', 'ticketId' => $model->id], ['class' => 'button']),
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>