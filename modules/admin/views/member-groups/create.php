<?php
/* @var $this yii\web\View */
/* @var $model app\models\MemberGroup */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => Yii::t('model', 'User Group'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="user-group-create">
    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>
</div>
