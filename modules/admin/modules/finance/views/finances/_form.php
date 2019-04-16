<?php

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
            <?= $form->field($model, 'type')->dropDownList(\app\modules\admin\modules\finance\models\Finance::typeOptions()) ?>

            <?= $form->field($model, 'source')->dropDownList(\app\modules\admin\modules\finance\models\Finance::sourceOptions()) ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'money')->textInput() ?>

            <?= $form->field($model, 'remittance_slip')->fileInput() ?>
        </div>
        <div class="entry">
            <?= $form->field($model, 'member_id')->dropDownList(\app\models\Member::map(), ['prompt' => '']) ?>

            <?= $form->field($model, 'status')->dropDownList(\app\modules\admin\modules\finance\models\Finance::statusOptions()) ?>
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
    no_results_text: '无匹配数据：',
    placeholder_text_multiple: '点击此处，在空白框内输入或选择会员帐号',
    width: '400px',
    search_contains: true,
    allow_single_deselect: true
});
EOT;
$this->registerJs($js);
