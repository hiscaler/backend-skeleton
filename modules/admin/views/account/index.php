<?php

use app\modules\admin\components\MessageBox;
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
    echo MessageBox::widget([
        'message' => $session->getFlash('notice'),
    ]);
else:
    ?>
    <div class="form-outside">
        <div class="form user-form">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'disabled' => 'disabled', 'readonly' => 'readonly', 'class' => 'disabled']) ?>

            <?php
            $extra = null;
            if (!$model->getIsNewRecord() && $model->avatar) {
                $extra = Html::img($model->avatar, ['alt' => $model->username, 'style' => 'width: 30px; height: 30px; position: absolute;']);
            }
            echo $form->field($model, 'avatar', [
                'template' => "{label}{input}$extra{error}",
            ])->fileInput();
            ?>

            <?= $form->field($model, 'real_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'remark')->textarea() ?>
            <div class="form-group buttons">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php endif; ?>