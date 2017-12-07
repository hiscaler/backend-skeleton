<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Modules');
$this->params['breadcrumbs'][] = $this->title;

$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
];
?>
<div class="module-index">

    <ul class="tabs-common">
        <li class="active"><a href="javascript:;" data-toggle="panel-installed">已安装模块</a></li>
        <li><a href="javascript:;" data-toggle="panel-notinstalled">待安装模块</a></li>
    </ul>

    <div class="panels">
        <div id="panel-installed" class="tab-pane">
            <ul>
                <?php foreach ($installedModules as $i => $module): ?>
                    <li id="module-<?= $module['alias'] ?>" class="widget-module">
                        <div class="hd">
                            <?= $module['name'] ?>
                            <span class="icon"><?= Html::img($module['icon'], ['src' => $module['name']]) ?></span>
                            <span class="buttons"><?= Html::a(Yii::t('module', 'Uninstall'), ['install', 'alias' => $module['alias']], ['class' => 'uninstall']) ?></span>
                        </div>
                        <div class="bd">
                            <p class="misc">
                                <span>作者：<?= $module['author'] ?></span>
                                <span>版本：<?= $module['version'] ?></span>
                                <span>URL：<?= $module['url'] ?></span>
                            </p>
                            <p class="description">
                                <?= $module['description'] ?>
                            </p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="panel-notinstalled" class="tab-pane" style="display: none">
            <ul>
                <?php foreach ($notInstalledModules as $i => $module): ?>
                    <li id="module-<?= $module['alias'] ?>" class="widget-module">
                        <div class="hd">
                            <?= $module['name'] ?>
                            <span class="icon"><?= Html::img($module['icon'], ['src' => $module['name']]) ?></span>
                            <span class="buttons"><?= Html::a(Yii::t('module', 'Install'), ['install', 'alias' => $module['alias']], ['class' => 'install']) ?></span>
                        </div>
                        <div class="bd">
                            <p class="misc">
                                <span>作者：<?= $module['author'] ?></span>
                                <span>版本：<?= $module['version'] ?></span>
                                <span>URL：<?= $module['url'] ?></span>
                            </p>
                            <p class="description">
                                <?= $module['description'] ?>
                            </p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

</div>
