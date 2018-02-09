<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LabelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
    ['label' => Yii::t('app', 'Search'), 'url' => '#'],
];
if ($dataProviders):
    ?>
    <div id="attribute-entity-attributes" class="attribute-entity-attributes">
        <?php
        $js = <<<'EOT'
    $(document).on('click', '#tabs-entity-attributes li a', function () {
         $t = $(this);
         $('.panel').hide();
         $('#' + $t.attr('data-key')).show();
         $t.parent().siblings().removeClass('active').end().addClass('active');

         return false;
     });
EOT;
        $this->registerJs($js);
        ?>
        <ul id="tabs-entity-attributes" class="tabs-common">
            <?php
            $i = 0;
            foreach ($dataProviders as $key => $dataProvider):
                $i++;
                ?>
                <li class="<?= $i == 1 ? 'active' : '' ?>"><a href="###" data-key="panel-<?= $key ?>"><?= $key ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php
        Pjax::begin([
            'linkSelector' => '#attribute-entity-attributes',
            'linkSelector' => '#ww0 a',
            'enablePushState' => false,
            'options' => [
                'id' => 'ww0',
            ]
        ]);
        ?>
        <div class="panels">
            <?php
            $i = 0;
            foreach ($dataProviders as $key => $dataProvider):
                $i++;
                ?>
                <div id="panel-<?= $key ?>" class="panel" style="<?= $i != 1 ? 'display: none' : '' ?>">
                    <?php
                    echo GridView::widget([
                        'id' => 'grid-view-entity-attributes',
                        'dataProvider' => $dataProvider,
                        'layout' => "{items}\n{pager}",
                        'rowOptions' => function ($model, $key, $index, $grid) {
                            return [
                                'data-key' => $key,
                                'data-entity-id' => $model['entityId'],
                                'data-model-name' => $model['modelName'],
                                'data-label-id' => $model['id'],
                            ];
                        },
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'contentOptions' => ['class' => 'serial-number']
                            ],
                            [
                                'attribute' => 'name',
                                'header' => Yii::t('label', 'Name')
                            ],
                            [
                                'attribute' => 'enabled',
                                'format' => 'boolean',
                                'header' => Yii::t('app', 'Enabled'),
                                'contentOptions' => ['class' => 'boolean pointer set-entity-label-handler'],
                                'headerOptions' => ['class' => 'last'],
                            ],
                        ],
                    ]);
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
    <?php
    $this->registerJs('yadjet.actions.toggle("table td.set-entity-label-handler img", "' . Url::toRoute('set') . '", {}, ["entity-id", "model-name", "label-id"]);');
else:
    echo \yii\helpers\Html::tag('div', '您还未设置推送位。', ['class' => 'notice']);
endif;
?>
