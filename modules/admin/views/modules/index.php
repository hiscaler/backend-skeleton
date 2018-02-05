<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Modules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-index">
    <ul class="tabs-common">
        <li class="active"><a href="javascript:;" data-toggle="panel-installed"><?= Yii::t('module', 'Installed modules') ?></a></li>
        <li><a href="javascript:;" data-toggle="panel-notinstalled"><?= Yii::t('module', 'Pending install modules') ?></a></li>
    </ul>
    <div class="panels">
        <div id="panel-installed" class="tab-panel">
            <ul>
                <?php foreach ($installedModules as $i => $module): ?>
                    <li id="module-<?= $module['alias'] ?>" class="widget-module">
                        <div class="hd">
                            <em><?= $module['name'] ?></em>
                            <span class="icon"><?= Html::img($module['icon'], ['src' => $module['name']]) ?></span>
                            <span class="buttons">
                                <?= Html::a(Yii::t('module', 'Uninstall'), ['uninstall', 'alias' => $module['alias']], ['class' => 'uninstall', 'data-key' => $module['alias'], 'data-url' => \yii\helpers\Url::toRoute(['install', 'alias' => $module['alias']])]) ?>
                                <?= Html::a(Yii::t('module', 'Upgrade'), ['upgrade', 'alias' => $module['alias']], ['class' => 'upgrade', 'data-key' => $module['alias'], 'data-name' => $module['name'], 'data-url' => \yii\helpers\Url::toRoute(['upgrade', 'alias' => $module['alias']])]) ?>
                            </span>
                        </div>
                        <div class="bd">
                            <p class="misc">
                                <span><?= Yii::t('module', 'Author') ?>：<?= $module['author'] ?></span>
                                <span><?= Yii::t('module', 'Version') ?>：<?= $module['version'] ?></span>
                                <span><?= Yii::t('module', 'Url') ?>：<a href="<?= $module['url'] ?>" target="_blank"><?= $module['url'] ?></a></span>
                            </p>
                            <p class="description">
                                <?= $module['description'] ?>
                            </p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="panel-notinstalled" class="tab-panel" style="display: none">
            <ul>
                <?php foreach ($notInstalledModules as $i => $module): ?>
                    <li id="module-<?= $module['alias'] ?>" class="widget-module">
                        <div class="hd">
                            <em><?= $module['name'] ?></em>
                            <span class="icon"><?= Html::img($module['icon'], ['src' => $module['name']]) ?></span>
                            <span class="buttons"><?= Html::a(Yii::t('module', 'Install'), ['install', 'alias' => $module['alias']], ['class' => 'install', 'data-key' => $module['alias'], 'data-url' => \yii\helpers\Url::toRoute(['uninstall', 'alias' => $module['alias']])]) ?></span>
                        </div>
                        <div class="bd">
                            <p class="misc">
                                <span><?= Yii::t('module', 'Author') ?>：<?= $module['author'] ?></span>
                                <span><?= Yii::t('module', 'Version') ?>：<?= $module['version'] ?></span>
                                <span><?= Yii::t('module', 'Url') ?>：<a href="<?= $module['url'] ?>" target="_blank"><?= $module['url'] ?></a></span>
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
<?php \app\modules\admin\components\JsBlock::begin() ?>
<script type="text/javascript">
    $(function () {
        var installText = '<?= Yii::t('module', 'Install')?>',
            uninstallText = '<?= Yii::t('module', 'Uninstall')?>';

        $('.install, .uninstall').on('click', function () {
            var $t = $(this);
            if ($t.hasClass('install')) {
                _doInstallUninstall($t);
            } else {
                var droptableMessage = '';
                <?php
                if (isset(Yii::$app->params['uninstall.module.after.droptable']) && Yii::$app->params['uninstall.module.after.droptable'] === true) {
                    echo "droptableMessage = '卸载后将同步删除模块相关数据表！！！';";
                }
                ?>
                layer.confirm('您是否确定卸载"' + $t.parent().parent().find('em').html() + '"模块？' + droptableMessage, {
                    btn: ['确定卸载', '取消'] //按钮
                }, function (index) {
                    _doInstallUninstall($t);
                    layer.close(index);
                }, function () {
                });
            }

            return false;
        });

        function _doInstallUninstall($t) {
            var url = $t.attr('href');
            console.info(url);
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                beforeSend: function (xhr) {
                    $.fn.lock();
                },
                success: function (response) {
                    if (response.success) {
                        var isInstall = $t.hasClass('install'),
                            $obj = $('#module-' + $t.attr('data-key'));
                        $t.text(isInstall ? uninstallText : installText)
                            .attr('href', $t.attr('data-url'))
                            .attr('data-url', url)
                            .removeClass(isInstall ? 'install' : 'uninstall')
                            .addClass(isInstall ? 'uninstall' : 'install');
                        var $tmp = $obj.clone(true, true);
                        $obj.remove();
                        if (isInstall) {
                            $('#panel-installed ul').append($tmp);
                        } else {
                            $('#panel-notinstalled ul').append($tmp);
                            $('#control-panel-module-' + $t.attr('data-key')).parent().remove();
                        }
                    } else {
                        layer.alert(response.error.message);
                    }
                    $.fn.unlock();
                }
            });
        }

        // 模块信息更新
        $('.upgrade').on('click', function () {
            var $t = $(this);
            $.ajax({
                type: 'POST',
                url: $t.attr('data-url'),
                dataType: 'json',
                beforeSend: function (xhr) {
                    $.fn.lock();
                },
                success: function (response) {
                    if (response.success) {
                        layer.alert($t.attr('data-name') + '模块更新成功。', {icon: 1});
                    } else {
                        layer.alert(response.error.message);
                    }
                    $.fn.unlock();
                }
            });

            return false;
        });
    });
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
