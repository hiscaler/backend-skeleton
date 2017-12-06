<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>        
        <title><?= Html::encode($this->title) ?></title>
        <style type="text/css">.ms-controller{visibility: hidden}</style>
        <!--[if lt IE 8]><link rel="stylesheet" href="<?php echo Yii::$app->getRequest()->getBaseUrl(); ?>/css/ie.css" type="text/css" media="screen, projection" /><![endif]-->
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>

        <div id="page-hd">
            <?= \app\widgets\Navigate::widget() ?>
            <?= \app\widgets\Header::widget() ?>
        </div>
        <div id="page-bd">
            <?= $content ?>
        </div>
        <div id="page-ft">
            <?= app\widgets\Footer::widget() ?>
        </div>
        <?php // app\widgets\QQOnline::widget() ?>
        <?php $this->endBody() ?>
        <script type="text/javascript">
            $(function () {
                Mai.urls.shoppingCart = {
                    index: '<?= Url::toRoute(['/shopping-cart/index']) ?>',
                    add: '<?= Url::toRoute(['/shopping-cart/add', 'itemId' => '_itemId']) ?>',
                    'delete': '<?= Url::toRoute(['/shopping-cart/delete', 'itemId' => '_itemId']) ?>',
                    changeQuantity: '<?= Url::toRoute(['/shopping-cart/change-quantity', 'itemId' => '_itemId']) ?>'
                };
                avalon.config.debug = <?= YII_DEBUG ? 'true' : 'false' ?>;
            });
        </script>
    </body>
</html>
<?php $this->endPage() ?>
