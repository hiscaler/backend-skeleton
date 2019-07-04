<?php

use app\models\Member;
use app\modules\admin\modules\finance\models\Finance;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\finance\models\Finance */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside">
    <div class="form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="entry">
            <?= $form->field($model, 'type')->dropDownList(Finance::typeOptions()) ?>

            <?= $form->field($model, 'source')->dropDownList(Finance::sourceOptions()) ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'money')->textInput(['type' => 'number'])->hint('单位为：分') ?>

            <?= $form->field($model, 'remittance_slip')->fileInput() ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'member_id')->dropDownList(Member::map('nickname'), ['prompt' => '']) ?>

            <?= $form->field($model, 'status')->dropDownList(Finance::statusOptions()) ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';
$this->registerJsFile($baseUrl . '/chosen/chosen.jquery.min.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerCssFile($baseUrl . '/chosen/chosen.min.css');
$js = <<<EOT
$('#finance-member_id').chosen({
    no_results_text: '没有符合条件的会员：',
    placeholder_text: '点击此处，在空白框内输入或选择会员帐号',
    width: '400px',
    search_contains: true,
    allow_single_deselect: true
});
EOT;
$this->registerJs($js);
