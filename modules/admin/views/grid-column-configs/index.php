<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GridColumnConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="grid-column-config-index">

    <?php
    Pjax::begin([
        'linkSelector' => '#pjax-grid-column-configs a',
        'enablePushState' => false,
        'options' => [
            'id' => 'pjax-grid-column-configs',
        ]
    ]);
    echo GridView::widget([
        'layout' => "{items}\n{pager}",
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number']
            ],
            [
                'attribute' => 'attribute',
                'label' => Yii::t('gridColumnConfig', 'Attribute'),
            ],
            [
                'attribute' => 'css_class',
                'label' => Yii::t('gridColumnConfig', 'CSS Class'),
            ],
            [
                'attribute' => 'visible',
                'format' => 'boolean',
                'headerOptions' => ['class' => 'last'],
                'contentOptions' => ['class' => 'boolean visible-handler pointer'],
                'label' => Yii::t('gridColumnConfig', 'Visible'),
            ],
        ],
    ]);
    $this->registerJs('yadjet.actions.toggle("table td.visible-handler img", "' . Url::toRoute('toggle') . '", {"name": "' . $name . '"});');
    Pjax::end();
    ?>

</div>