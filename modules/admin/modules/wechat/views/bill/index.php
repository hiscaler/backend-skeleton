<?php

use app\modules\admin\modules\wechat\models\Bill;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = '对账单管理';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="bill-index">
    <div class="form-outside form-search form-layout-column">
        <div class="bill-search form">
            <?php $form = ActiveForm::begin([
                'id' => 'form-bill',
                'action' => ['index'],
                'method' => 'POST',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>
            <div class="entry">
                <?= $form->field($model, 'date')->textInput() ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'type')->dropDownList(Bill::typeOptions()) ?>
            </div>
            <div class="form-group buttons">
                <?= Html::submitButton('下载对账单', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>