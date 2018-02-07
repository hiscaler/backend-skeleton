<?php
$this->context->layout = false;
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */

/* @var $exception Exception */

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;

app\modules\admin\assets\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $name . ' - ' . \app\models\Lookup::getValue('custom.site.name') ?: Yii::$app->name ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="site-error">
    <h1><?= $name ?></h1>
    <div class="message">
        <?= nl2br(Html::encode($message)) ?>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
