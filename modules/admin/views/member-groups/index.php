<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MemberGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'User Groups');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];
?>
<div class="user-group-index">
    <ul class="tabs-common">
        <?php
        $i = 1;
        foreach (\app\models\MemberGroup::typeOptions() as $key => $name):
            $cssClass = $i == 1 ? ' class="active"' : '';
            $i++;
            ?>
            <li<?php echo $cssClass; ?>><a href="javascript:;" data-toggle="panel-user-group-<?= $key ?>"><?= $name ?></a></li>
        <?php endforeach; ?>
    </ul>
    <div class="panels">
        <div id="panel-user-group-<?= \app\models\MemberGroup::TYPE_USER_GROUP ?>" class="tab-panel">
            <?=
            GridView::widget([
                'dataProvider' => $userGroupDataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'contentOptions' => ['class' => 'serial-number']
                    ],
                    [
                        'attribute' => 'alias',
                        'header' => Yii::t('memberGroup', 'Alias'),
                        'contentOptions' => ['class' => 'alias'],
                    ],
                    [
                        'attribute' => 'name',
                        'header' => Yii::t('memberGroup', 'Name'),
                    ],
                    [
                        'attribute' => 'min_credits',
                        'header' => Yii::t('memberGroup', 'Min Credits'),
                        'contentOptions' => ['class' => 'number'],
                    ],
                    [
                        'attribute' => 'max_credits',
                        'header' => Yii::t('memberGroup', 'Max Credits'),
                        'contentOptions' => ['class' => 'number'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'header' => Yii::t('app', 'Created At'),
                        'format' => 'date',
                        'contentOptions' => ['class' => 'date']
                    ],
                    [
                        'attribute' => 'updated_at',
                        'header' => Yii::t('app', 'Updated At'),
                        'format' => 'date',
                        'contentOptions' => ['class' => 'date']
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'headerOptions' => array('class' => 'buttons-2 last'),
                    ],
                ],
            ]);
            ?>
        </div>
        <div id="panel-user-group-<?= \app\models\MemberGroup::TYPE_SYSTEM_GROUP ?>" class="tab-panel" style="display: none">
            <?=
            GridView::widget([
                'dataProvider' => $systemGroupDataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'contentOptions' => ['class' => 'serial-number']
                    ],
                    [
                        'attribute' => 'alias',
                        'header' => Yii::t('userGroup', 'Alias'),
                        'contentOptions' => ['class' => 'alias'],
                    ],
                    [
                        'attribute' => 'name',
                        'header' => Yii::t('userGroup', 'Name'),
                    ],
                    [
                        'attribute' => 'min_credits',
                        'header' => Yii::t('userGroup', 'Min Credits'),
                        'contentOptions' => ['class' => 'number'],
                    ],
                    [
                        'attribute' => 'max_credits',
                        'header' => Yii::t('userGroup', 'Max Credits'),
                        'contentOptions' => ['class' => 'number'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'header' => Yii::t('app', 'Created At'),
                        'format' => 'date',
                        'contentOptions' => ['class' => 'date']
                    ],
                    [
                        'attribute' => 'updated_at',
                        'header' => Yii::t('app', 'Updated At'),
                        'format' => 'date',
                        'contentOptions' => ['class' => 'date']
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'headerOptions' => array('class' => 'buttons-2 last'),
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
</div>
