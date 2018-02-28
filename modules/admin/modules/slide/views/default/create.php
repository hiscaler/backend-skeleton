<?php
/* @var $this yii\web\View */
/* @var $model \app\modules\admin\modules\slide\models\Slide */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('slide', 'Slides'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];
?>
<div class="slide-create">
    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>
</div>
