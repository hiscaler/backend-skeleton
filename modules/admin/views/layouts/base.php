<?php
/* @var $this \yii\web\View */

/* @var $content string */

use app\modules\admin\widgets\MainMenu;
use yii\helpers\Html;

app\modules\admin\assets\AppAsset::register($this);

$baseUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin';
$siteName = \app\models\Lookup::getValue('custom.site.name') ?: Yii::$app->name;
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?> - <?= $siteName ?></title>
        <?php $this->head() ?>
    </head>
    <body id="mts-app">
    <?php $this->beginBody() ?>
    <div id="page-hd">
        <div id="page">
            <!-- Header -->
            <div id="header">
                <div id="logo"><?php echo Html::a(Html::img($baseUrl . '/images/logo.png'), Yii::$app->homeUrl); ?></div>
                <div id="main-menu">
                    <?= MainMenu::widget() ?>
                </div>
                <div id="header-account-manage">
                    <?= app\modules\admin\widgets\Toolbar::widget(); ?>
                </div>
            </div>
            <!-- // Header -->
        </div>
    </div>
    <div id="page-bd">
        <div class="container">
            <?= $content ?>
        </div>
    </div>
    <div id="page-ft">
        <div id="footer">
            Copyright &copy; <?= date('Y'); ?> by <?= $siteName ?> All Rights Reserved.
        </div>
    </div>
    <?php $this->endBody() ?>
    <script type="text/javascript">
        yadjet.icons.boolean = ['<?= $baseUrl ?>/images/no.png', '<?= $baseUrl ?>/images/yes.png'];
    </script>
    </body>
    </html>
<?php $this->endPage() ?>