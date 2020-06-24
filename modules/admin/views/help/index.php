<?php

use yii\widgets\Breadcrumbs;

$this->title = $doc['title'];
$this->params['breadcrumbs'][] = ['label' => '帮助中心', 'url' => ['help/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(Yii::$app->getRequest()->getBaseUrl() . '/admin/css/help.css');
$this->beginContent('@app/modules/admin/views/layouts/base.php');
?>
<div class="layout grid-s6m0e0">
    <div class="col-main">
        <div class="main-wrap">
            <?php
            if (isset($this->params['breadcrumbs']) && $this->params['breadcrumbs']) {
                echo '<div class="breadcrumbs clearfix">';
                echo Breadcrumbs::widget(
                    [
                        'itemTemplate' => "<li>{link}<i>&raquo;</i></li>",
                        'homeLink' => [
                            'label' => Yii::t('app', 'Homepage'),
                            'url' => ['/admin/default/index'],
                        ],
                        'links' => $this->params['breadcrumbs'],
                    ]);
                echo '</div>';
            }
            ?>
            <div class="container">
                <div class="inner">
                    <div id="help-body-render">
                        <?= $doc['content'] ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sub">
        <div class="control-panel">
            <div class="inner">
                <div class="title shortcut">帮助中心</div>
                <div class="shortcuts">
                    <?= yii\widgets\Menu::widget([
                        'items' => $sections,
                        'itemOptions' => ['class' => 'clearfix'],
                        'firstItemCssClass' => 'first',
                        'lastItemCssClass' => 'last',
                        'activateParents' => true,
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endContent(); ?>
