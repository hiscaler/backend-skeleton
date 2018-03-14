<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\news\models\PostRawSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '资讯管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];

$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';
?>
<div class="news-index">
    <?php // $this->render('_search', ['model' => $searchModel, 'categories' => $categories]); ?>
    <?php Pjax::begin([
        'formSelector' => '#form-post',
        'linkSelector' => '#grid-view-post a',
    ]); ?>
    <?= GridView::widget([
        'id' => 'grid-view-post',
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            'channel',
            [
                'attribute' => 'job',
                'format' => 'ntext',
                'value' => function ($model) {
                    $obj = unserialize($model['job']);
                    $vars = [];
                    foreach (get_object_vars($obj) as $key => $value) {
                        $vars[] = "$key: $value";
                    }

                    return "Object: " . get_class($obj) . PHP_EOL . implode(PHP_EOL, $vars);
                }
            ],
            [
                'attribute' => 'pushed_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'ttr',
                'contentOptions' => ['style' => 'width: 30px;']
            ],
            [
                'attribute' => 'delay',
                'contentOptions' => ['style' => 'width: 30px;']
            ],
            [
                'attribute' => 'priority',
                'contentOptions' => ['style' => 'width: 30px;']
            ],
            [
                'attribute' => 'reserved_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'attribute' => 'attempt',
                'contentOptions' => ['class' => 'number'],
            ],

            [
                'attribute' => 'done_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php \app\modules\admin\components\JsBlock::begin() ?>
<script type="text/javascript">
    $(function () {
        yadjet.actions.toggle("table td.post-enabled-handler img", "<?= Url::toRoute('toggle') ?>");
        yadjet.actions.toggle("table td.post-enabled-comment-handler img", "<?= Url::toRoute('toggle-comment') ?>");

        jQuery(document).on('click', 'a.setting-entity-labels', function () {
            var $this = $(this);
            $.ajax({
                type: 'GET',
                url: $this.attr('href'),
                beforeSend: function (xhr) {
                    $.fn.lock();
                }, success: function (response) {
                    layer.open({
                        skin: 'layer-fix',
                        title: $this.attr('title'),
                        content: response,
                        move: false
                    });
                    $.fn.unlock();
                }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                    layer.alert('[ ' + XMLHttpRequest.status + ' ] ' + XMLHttpRequest.responseText, {icon: 2});
                    $.fn.unlock();
                }
            });

            return false;
        });
    });
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
