<?php
/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Change Password');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>

<div class="user-create">

    <?=
    $this->render('_changePasswordForm', [
        'user' => $user,
        'model' => $model,
    ]);
    ?>

</div>
