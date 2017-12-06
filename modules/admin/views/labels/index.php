<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LabelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Labels');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
?>
    <div class="labels-index">

        <?= $this->render('_search', ['model' => $searchModel]) ?>

        <?php
        Pjax::begin([
            'formSelector' => '#form-attribute-search',
        ]);
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['class' => 'serial-number']
                ],
                [
                    'attribute' => 'ordering',
                    'contentOptions' => ['class' => 'number'],
                ],
                [
                    'attribute' => 'alias',
                    'contentOptions' => ['style' => 'width: 60px'],
                ],
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return "<span class=\"pk\">[ {$model['id']} ]</span>" . Html::a($model['name'], ['update', 'id' => $model['id']]);
                    }
                ],
                [
                    'attribute' => 'frequency',
                    'contentOptions' => ['class' => 'number'],
                ],
                [
                    'attribute' => 'enabled',
                    'format' => 'boolean',
                    'contentOptions' => ['class' => 'boolean pointer enabled-handler'],
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
                    'template' => '{update} {delete}',
                    'headerOptions' => ['class' => 'buttons-2 last'],
                ],
            ],
        ]);
        Pjax::end();
        ?>

    </div>

<?php \app\modules\admin\components\JsBlock::begin() ?>
    <script type="text/javascript">
        yadjet.actions.toggle("table td.enabled-handler img", "<?= Url::toRoute('toggle') ?>");
    </script>
<?php \app\modules\admin\components\JsBlock::end() ?>