<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Label */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Attributes');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="entity-labels-list">
        <ul class="clearfix">
            <li<?= empty($labelId) ? ' class="active"' : '' ?>><?= Html::a(Yii::t('app', 'List'), ['entities', 'modelName' => $modelName]); ?></li>
            <?php
            $max = count($lables);
            $i = 0;
            foreach ($labels as $key => $label):
                $i++;
                $cssClass = '';
                if ($key == $attributeId) {
                    $cssClass = 'active';
                }
                if ($i == $max) {
                    $cssClass .= (!empty($cssClass) ? ' ' : '') . 'last';
                }
                ?>
                <li<?= !empty($cssClass) ? ' class="' . $cssClass . '"' : '' ?>><?= Html::a($label['name'] . " [ {$label['alias']} ]", ['entities', 'modelName' => $modelName, 'labelId' => $key]); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="attribute-index">
        <?php
        Pjax::begin();
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
                    'label' => Yii::t('app', 'Ordering'),
                ],
                [
                    'attribute' => 'title',
                    'label' => Yii::t('app', 'Title'),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return "<span class=\"pk\">[ {$model['entity_id']} ]</span>" . $model['title'];
                    },
                ],
                [
                    'attribute' => 'enabled',
                    'format' => 'boolean',
                    'contentOptions' => ['class' => 'boolean pointer enabled-handler'],
                    'label' => Yii::t('app', 'Enabled'),
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'headerOptions' => ['class' => 'last'],
                    'contentOptions' => ['class' => 'btn'],
                ],
            ],
        ]);
        Pjax::end();
        ?>
    </div>
<?php
$this->registerJs('yadjet.actions.toggle("table td.enabled-handler img", "' . Url::toRoute('toggle') . '");');
