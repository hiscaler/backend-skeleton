<?php
/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Change Password');

$session = Yii::$app->getSession();
if ($session->hasFlash('notice')):
    echo \app\modules\admin\components\MessageBox::widget([
        'message' => $session->getFlash('notice'),
        'showCloseButton' => false,
    ]);
else:
    ?>
    <div class="user-create">
        <?=
        $this->render('_changePasswordForm', [
            'model' => $model,
        ]);
        ?>
    </div>
<?php endif; ?>
