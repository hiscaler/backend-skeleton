<?php

use app\modules\admin\components\MessageBox;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\forms\CacheCleanForm */
/* @var $form yii\widgets\ActiveForm */

$this->title = '缓存管理';
$this->params['breadcrumbs'][] = $this->title;

$session = Yii::$app->getSession();
if ($session->hasFlash('notice')):
    echo MessageBox::widget([
        'message' => $session->getFlash('notice'),
    ]);
else:
    echo MessageBox::widget([
        'message' => '如果您选择清理`全部缓存`，根据缓存量的多少，所需时间将不定（最长清理 6 分钟），请耐心等待。',
    ]);
    ?>
    <div class="form-outside">
        <div class="form user-form">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'type')->dropDownList($model::typeOptions()) ?>
            <div class="form-group buttons">
                <?= Html::submitButton('清理', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php endif; ?>