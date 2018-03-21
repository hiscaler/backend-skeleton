<?php

/* @var $this yii\web\View */

$this->title = '数据库管理';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->getSession();
if ($session->hasFlash('notice')) {
    echo \app\modules\admin\components\MessageBox::widget([
        'message' => $session->getFlash('notice'),
    ]);
}

if ($histories):
    $this->params['menus'] = [
        ['label' => '备份数据库', 'url' => ['backup'], 'htmlOptions' => ['class' => 'btn-db-backup']],
        ['label' => '清理所有备份', 'url' => ['clean'], 'htmlOptions' => ['data-method' => 'POST', 'data-confirm' => '是否确认清理掉所有备份？']],
    ];
    ?>
    <div class="grid-view">
        <table class="table">
            <thead>
            <tr>
                <th>序号</th>
                <th>备份时间</th>
                <th>名称</th>
                <th class="buttons-2 last">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($histories as $i => $history): ?>
                <tr>
                    <td class="serial-number"><?= $i + 1 ?></td>
                    <td class="datetime"><?= $history['date'] ?></td>
                    <td><?= $history['name'] ?></td>
                    <td>
                        <a data-method="POST" data-confirm="是否确认恢复该备份？" title="恢复数据库备份" href="<?= \yii\helpers\Url::toRoute(['restore', 'name' => $history['name']]) ?>"><span class="glyphicon glyphicon-restore"></span></a>
                        <a data-method="POST" data-confirm="是否确认删除该备份？" title="删除数据库备份" href="<?= \yii\helpers\Url::toRoute(['delete', 'name' => $history['name']]) ?>"><span class="glyphicon glyphicon-trash"></span></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <?= \app\modules\admin\components\MessageBox::widget([
        'message' => '您还没有备份过数据库。<a class="button btn-db-backup" href="' . \yii\helpers\Url::toRoute('backup') . '">备份数据库</a>',
    ]) ?>
<?php endif; ?>
<?php \app\modules\admin\components\JsBlock::begin() ?>
    <script type="text/javascript">
        $(function () {
            $('.btn-db-backup').click(function () {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('href'),
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        $.fn.lock();
                    }, success: function (response) {
                        if (response.success) {
                            layer.msg('数据库备份成功。共操作 ' + response.data.processTablesCount + ' 个表，合计备份 ' + response.data.processRowsCount + ' 条数据。', {
                                icon: 1,
                                time: 6000,
                                btn: ['关闭']
                            }, function () {
                                window.document.location.href = '<?= \yii\helpers\Url::toRoute(['index']) ?>';
                            });
                        } else {
                            layer.alert(response.error.message);
                        }
                        $.fn.unlock();
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        layer.alert('[ ' + XMLHttpRequest.status + ' ] ' + XMLHttpRequest.responseText);
                        $.fn.unlock();
                    }
                });

                return false;
            });
        });
    </script>
<?php \app\modules\admin\components\JsBlock::end() ?>