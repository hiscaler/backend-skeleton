<?php
/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app', 'User Credit Logs');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Credit Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>

<div class="user-create">

    <?=
    $this->render('_addCreditForm', [
        'model' => $model,
    ]);
    ?>

</div>
