<?php
/* @var $this yii\web\View */
/* @var $model \app\modules\admin\forms\ChangePasswordForm */
/* @var $member \app\models\Member */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Change Password');

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="user-create">
    <?=
    $this->render('_changePasswordForm', [
        'member' => $member,
        'model' => $model,
    ]);
    ?>
</div>
