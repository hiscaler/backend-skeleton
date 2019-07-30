<?php
/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('app', 'Change Password');
$this->params['breadcrumbs'][] = ['label' => '帐户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

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
