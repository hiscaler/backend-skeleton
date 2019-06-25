<?php

use app\modules\admin\components\CssBlock;
use app\modules\admin\components\MessageBox;
use yii\helpers\Html;
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
$formatter = Yii::$app->getFormatter();
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
                <ul class="attachments clearfix">
                    <?php foreach ($attachments as $attachment): ?>
                        <li><img src="<?= $attachment['path'] ?>" alt=""></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <?= MessageBox::widget([
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
                                <td class="datetime"><?= $formatter->asDatetime($message['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <?= MessageBox::widget([
                    'message' => '暂无互动消息   ' . Html::a('回复消息', ['messages/create', 'ticketId' => $model->id], ['class' => 'button']),
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php CssBlock::begin() ?>
    <style type="text/css">
        .attachments {
        }

        .attachments li {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        .attachments li img {
            max-width: 100%;
            max-height: 300px;
        }
    </style>
<?php CssBlock::end() ?>