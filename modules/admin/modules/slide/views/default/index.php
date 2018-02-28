<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \app\modules\admin\modules\slide\models\SlideSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('slide', 'Slides');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
<div class="slide-index">
    <?= $this->render('_search', ['model' => $searchModel, 'categories' => $categories]); ?>
    <?php
    Pjax::begin([
        'formSelector' => '#form-slide-search',
    ]);
    ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table table-striped'
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number'],
            ],
            [
                'attribute' => 'ordering',
                'contentOptions' => ['class' => 'ordering'],
            ],
            [
                'attribute' => 'category.name',
                'contentOptions' => ['class' => 'category-name'],
                'visible' => $categories
            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<span class=\"pk\">[ {$model['id']} ]</span>" . Html::a($model['title'], ['view', 'id' => $model['id']]);
                }
            ],
            [
                'attribute' => 'url',
                'format' => 'url',
                'contentOptions' => ['class' => 'url'],
            ],
            [
                'attribute' => 'url_open_target_text',
                'contentOptions' => ['class' => 'url-open-target center'],
            ],
            [
                'attribute' => 'enabled',
                'format' => 'boolean',
                'contentOptions' => ['class' => 'boolean pointer boolean-handler'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['class' => 'buttons-3 last'],
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>

<?php \app\modules\admin\components\JsBlock::begin() ?>
    <script type="text/javascript">
        yadjet.actions.toggle("table td.boolean-handler img", "<?= \yii\helpers\Url::toRoute('toggle') ?>");
    </script>
<?php \app\modules\admin\components\JsBlock::end() ?>