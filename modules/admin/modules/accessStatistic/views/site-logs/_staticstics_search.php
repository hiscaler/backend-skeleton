<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="form-outside form-search form-layout-column">
    <div class="access-statistic-site-log-search form">
        <?php $form = ActiveForm::begin([
            'action' => ['statistics'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>
        <div class="entry">
            <div class="form-group">
                <?= Html::label('间隔时间', 'hours', ['class' => 'control-label']) ?>
                <?= Html::textInput('hours', $hours, ['class' => 'form-control']) ?> 小时
            </div>
        </div>
        <div class="form-group buttons">
            <?= Html::submitButton('确定', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
