<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\modules\classicCase\models\ClassicCaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '案例管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];

$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';
?>
<div class="classic-case-index">
    <?php Pjax::begin(); ?>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'ordering',
                'contentOptions' => ['class' => 'ordering'],
            ],
            [
                'attribute' => 'category.name',
                'contentOptions' => ['class' => 'category-name'],
            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) {
                    $output = "<span class=\"pk\">[ {$model['id']} ]</span>" . Html::a($model['title'], ['default/update', 'id' => $model['id']], ['data-pjax' => 0]);
                    $words = [];
                    foreach ($model['customLabels'] as $attr) {
                        $words[] = $attr['name'];
                    }
                    $sentence = Inflector::sentence($words, '、', null, '、');
                    if (!empty($sentence)) {
                        $sentence = "<span class=\"labels\">{$sentence}</span>";
                    }

                    return $sentence . $output;
                },
            ],
            [
                'attribute' => 'enabled',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean pointer boolean-handler'],
            ],
            [
                'attribute' => 'clicks_count',
                'contentOptions' => ['class' => 'number'],
            ],
            [
                'attribute' => 'published_at',
                'format' => 'datetime',
                'contentOptions' => ['class' => 'datetime']
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model) {
                    return $model['creater']['nickname'];
                },
                'contentOptions' => ['class' => 'username']
            ],
            [
                'attribute' => 'created_at',
                'format' => 'date',
                'contentOptions' => ['class' => 'date']
            ],
            [
                'attribute' => 'updated_by',
                'value' => function ($model) {
                    return $model['updater']['nickname'];
                },
                'contentOptions' => ['class' => 'username']
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'date',
                'contentOptions' => ['class' => 'date']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {entityLabels} {update} {delete}',
                'buttons' => [
                    'entityLabels' => function ($url, $model, $key) use ($baseUrl) {
                        return Html::a(Html::img($baseUrl . '/images/labels.png'), ['/admin/entity-labels/index', 'entityId' => $model['id'], 'modelName' => $model->className2Id()], ['title' => Yii::t('app', 'Entity Labels'), 'class' => 'setting-entity-labels', 'data-pjax' => '0']);
                    },
                ],
                'headerOptions' => ['class' => 'buttons-4 last'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php \app\modules\admin\components\JsBlock::begin() ?>
    <script type="text/javascript">
        $(function () {
            yadjet.actions.toggle("table td.boolean-handler img", "<?= Url::toRoute('toggle') ?>");

            jQuery(document).on('click', 'a.setting-entity-labels', function () {
                var $this = $(this);
                $.ajax({
                    type: 'GET',
                    url: $this.attr('href'),
                    beforeSend: function (xhr) {
                        $.fn.lock();
                    }, success: function (response) {
                        layer.open({
                            title: $this.attr('title'),
                            content: response,
                            lock: true,
                            padding: '10px'
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