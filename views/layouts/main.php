<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\widgets\Header;
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
        <!--[if lt IE 8]><link rel="stylesheet" href="<?php echo Yii::$app->getRequest()->getBaseUrl(); ?>/css/ie.css" type="text/css" media="screen, projection" /><![endif]-->
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>

        <div id="page-hd">
            <?= Header::widget() ?>
        </div>
        <div id="page-bd">
            <div class="container">
                <div id="page-left">
                    <div class="wrapper">

                    </div>
                    <div class="clearfix">
                    </div>
                </div>
                <div id="page-right">
                    <div id="innerpage">
                        <?php
                        if (isset($this->params['breadcrumbs']) && $this->params['breadcrumbs']) {
                            echo '<div class="breadcrumbs clearfix">';
                            echo yii\widgets\Breadcrumbs::widget(
                                [
                                    'itemTemplate' => "<li>{link}<i>&raquo;</i></li>",
                                    'homeLink' => [
                                        'label' => '首页',
                                        'url' => Yii::$app->homeUrl,
                                    ],
                                    'links' => $this->params['breadcrumbs'],
                                    'options' => ['class' => 'breadcrumb clearfix']
                            ]);
                            echo '</div>';
                        }
                        ?>
                        <div id="innerpage-body">
                            <?= $content ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="page-ft">
            <?= app\widgets\Footer::widget() ?>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
