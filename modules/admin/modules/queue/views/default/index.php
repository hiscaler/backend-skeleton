<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '队列任务管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => '批量删除', 'url' => ['batch-delete'], 'htmlOptions' => ['class' => 'btn-batch-delete']],
];
?>
<div class="news-index">
    <?= $this->render('_search', ['channel' => $channel]); ?>
    <?php Pjax::begin([
        'formSelector' => '#form-queue-search',
        'linkSelector' => '#grid-view-queues a',
    ]); ?>
    <?= GridView::widget([
        'id' => 'grid-view-queues',
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'checkbox-column']
            ],
            [
                'attribute' => 'channel',
                'header' => '频道',
                'contentOptions' => ['style' => 'width: 80px', 'class' => 'center']
            ],
            [
                'attribute' => 'job',
                'header' => '任务',
                'format' => 'raw',
                'value' => function ($model) {
                    $obj = unserialize($model['job']);
                    $vars = [];
                    foreach (get_object_vars($obj) as $key => $value) {
                        $vars[] = "<em class='badges badges-gray'>$key: $value</em>";
                    }

                    return "<em class='badges badges-red'>{$model['id']}</em> " . get_class($obj) . PHP_EOL . implode(PHP_EOL, $vars);
                }
            ],
            [
                'attribute' => 'ttr',
                'header' => 'TTR',
                'contentOptions' => ['style' => 'width: 30px;', 'class' => 'center']
            ],
            [
                'attribute' => 'delay',
                'header' => '延时（秒）',
                'contentOptions' => ['style' => 'width: 70px;', 'class' => 'center']
            ],
            [
                'attribute' => 'priority',
                'header' => '权重',
                'contentOptions' => ['style' => 'width: 40px;', 'class' => 'center']
            ],
            [
                'attribute' => 'pushed_at',
                'header' => '入队时间',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'reserved_at',
                'header' => '上次运行时间',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'attempt',
                'header' => '尝试次数',
                'contentOptions' => ['class' => 'number center'],
            ],
            [
                'attribute' => 'done_at',
                'header' => '完成时间',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'headerOptions' => ['class' => 'button-1 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php \app\modules\admin\components\JsBlock::begin() ?>
<script type="text/javascript">
    $(function () {
        // 批量删除
        $('.btn-batch-delete').on('click', function () {
            var ids = $('#grid-view-queues').yiiGridView('getSelectedRows');
            if (ids.length) {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('href'),
                    data: {ids: ids.toString()},
                    beforeSend: function (xhr) {
                        $.fn.lock();
                    }, success: function (response) {
                        if (response.success) {
                            window.location.reload(true);
                        } else {
                            layer.alert(response.error.message);
                        }
                        $.fn.unlock();
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        layer.alert('[ ' + XMLHttpRequest.status + ' ] ' + XMLHttpRequest.responseText, {icon: 2});
                        $.fn.unlock();
                    }
                });
            } else {
                layer.alert('请选择您要批量操作的数据。');
            }

            return false;
        });
    });
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>

