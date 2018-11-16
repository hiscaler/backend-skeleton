<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FileUploadConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'File Upload Configs');
$this->params['breadcrumbs'][] = Yii::t('app', 'File Upload Configs');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];

$formatter = Yii::$app->getFormatter();
$maxFileSizeBit = $formatter->asDecimal($maxFileSize);
?>
<?= \app\modules\admin\components\MessageBox::widget([
    'title' => '服务器文件上传设置',
    'message' => [
        "最多可同时上传文件总数量：$maxFiles 个；",
        "最大可同时上传文件总大小：" . Yii::$app->getFormatter()->asShortSize($maxFileSize) . '。',
    ],
    'showCloseButton' => false,
]) ?>
<div class="upload-config-index">
    <?= $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    Pjax::begin([
        'formSelector' => '#form-upload-configs-search',
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['class' => 'serial-number'],
            ],
            [
                'attribute' => 'type',
                'format' => 'fileUploadConfigType',
                'contentOptions' => ['class' => 'center', 'style' => 'width: 40px'],
            ],
            [
                'attribute' => 'model_name',
                'format' => 'modelName',
                'contentOptions' => ['class' => 'model-name center'],
            ],
            [
                'attribute' => 'attribute',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model['attribute'], ['update', 'id' => $model['id']]);
                },
                'contentOptions' => ['class' => 'file-upload-config-attribute']
            ],
            'extensions',
            [
                'attribute' => 'size',
                'format' => 'raw',
                'value' => function ($model) use ($formatter, $maxFileSizeBit) {
                    $v = $formatter->asShortSize($model['min_size']) . ' ~ ';
                    if ($formatter->asDecimal($model['max_size']) > $maxFileSizeBit) {
                        $v .= '<font color="red">' . $formatter->asShortSize($model['max_size']) . '</font>';
                    } else {
                        $v .= $formatter->asShortSize($model['max_size']);
                    }

                    return $v;
                },
                'contentOptions' => ['class' => 'file-upload-config-size center']
            ],
            [
                'attribute' => 'thumb',
                'value' => function ($model) {
                    return ($model['thumb_width'] && $model['thumb_height']) ? "W:{$model['thumb_width']}  H:{$model['thumb_height']}" : '';
                },
                'contentOptions' => ['class' => 'file-upload-config-thumb center']
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
