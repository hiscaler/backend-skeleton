<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$this->title = '帐号资料';
$this->params['breadcrumbs'][] = ['label' => '帐户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$session = Yii::$app->getSession();
if ($session->hasFlash('notice')):
    echo \app\modules\admin\components\MessageBox::widget([
        'message' => $session->getFlash('notice'),
    ]);
else:
    ?>
    <div class="form-outside">
        <div class="form user-form">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'disabled' => 'disabled', 'readonly' => 'readonly', 'class' => 'disabled']) ?>

            <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            <div class="form-group buttons">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php endif; ?>