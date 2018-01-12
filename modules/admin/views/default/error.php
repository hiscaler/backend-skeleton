<?php
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */

/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;

$this->params['breadcrumbs'][] = Yii::t('app', 'Prompt Message');
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="message">
        <?= nl2br(Html::encode($message)) ?>
    </div>
</div>
