<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$this->params['breadcrumbs'][] = '帐号资料';

$session = Yii::$app->session;
if ($session->hasFlash('success')):
    echo \app\modules\admin\components\MessageBox::widget([
        'message' => $session->getFlash('success'),
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