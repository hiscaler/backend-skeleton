<?php

use app\helpers\Config;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Modules');
$this->params['breadcrumbs'][] = $this->title;

$iconAPI = Yii::$app->getRequest()->getBaseUrl() . '/admin/images/api.png';
?>
<div class="module-index">
    <ul class="tabs-common">
        <li id="tab-installed" class="active"><a href="javascript:;" data-toggle="panel-installed"><?= Yii::t('module', 'Installed modules') ?><span class="badges badges-red"><?= count($installedModules) ?></span></a></li>
        <li id="tab-not-installed"><a href="javascript:;" data-toggle="panel-notinstalled"><?= Yii::t('module', 'Pending install modules') ?><span class="badges badges-red"><?= count($notInstalledModules) ?></span></a></li>
    </ul>
    <div class="panels">
        <div id="panel-installed" class="tab-panel">
            <?php if ($installedModules): ?>
                <ul>
                    <?php foreach ($installedModules as $i => $module): ?>
                        <li id="module-<?= $module['alias'] ?>" class="widget-module clearfix">
                            <div class="hd">
                                <em class="<?= $module['error'] ? 'error' : null ?>"><?= $module['name'] ?></em>
                                <span class="icon">
                                   <?= Html::img($module['icon'], ['alt' => $module['name']]) ?>
                                   <?php if ($module['enabled_api']) {
                                       echo Html::img($iconAPI, ['class' => 'api', 'title' => '启用 API']);
                                   } ?>
                                </span>
                                <span class="buttons">
                                    <?= Html::a(Yii::t('module', 'Uninstall'), ['uninstall', 'alias' => $module['alias']], ['class' => 'uninstall', 'data-key' => $module['alias'], 'data-url' => \yii\helpers\Url::toRoute(['install', 'alias' => $module['alias']])]) ?>
                                    <?= Html::a(Yii::t('module', 'Upgrade'), ['upgrade', 'alias' => $module['alias']], ['class' => 'upgrade', 'data-key' => $module['alias'], 'data-name' => $module['name'], 'data-url' => \yii\helpers\Url::toRoute(['upgrade', 'alias' => $module['alias']])]) ?>
                                </span>
                            </div>
                            <div class="bd">
                                <p class="misc">
                                    <span><?= Yii::t('module', 'Author') ?>：<?= $module['author'] ?></span>
                                    <span><?= Yii::t('module', 'Version') ?>：<?= $module['version'] ?></span>
                                    <?php if ($module['url']): ?>
                                        <span><?= Yii::t('module', 'Url') ?>：<a href="<?= $module['url'] ?>" target="_blank"><?= $module['url'] ?></a></span>
                                    <?php endif; ?>
                                </p>
                                <?php if ($module['description']): ?>
                                    <div class="description">
                                        <div class="inner"><?= $module['description'] ?></div>
                                        <span class="more more-open">&nbsp;</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="notice">您还没有安装任何模块。</div>
            <?php endif; ?>
        </div>
        <div id="panel-notinstalled" class="tab-panel" style="display: none">
            <?php if ($notInstalledModules): ?>
                <ul>
                    <?php foreach ($notInstalledModules as $i => $module): ?>
                        <li id="module-<?= $module['alias'] ?>" class="widget-module clearfix">
                            <div class="hd">
                                <em><?= $module['name'] ?></em>
                                <span class="icon">
                                    <?= Html::img($module['icon'], ['alt' => $module['name']]) ?>
                                    <?php if ($module['enabled_api']) {
                                        echo Html::img($iconAPI, ['class' => 'api', 'title' => '启用 API']);
                                    } ?>
                                </span>
                                <span class="buttons">
                                    <?= Html::a(Yii::t('module', 'Install'), ['install', 'alias' => $module['alias']], ['class' => 'install', 'data-key' => $module['alias'], 'data-url' => \yii\helpers\Url::toRoute(['uninstall', 'alias' => $module['alias']])]) ?>
                                    <?= Html::a(Yii::t('module', 'Upgrade'), ['upgrade', 'alias' => $module['alias']], ['class' => 'upgrade', 'data-key' => $module['alias'], 'data-name' => $module['name'], 'data-url' => \yii\helpers\Url::toRoute(['upgrade', 'alias' => $module['alias']]), 'style' => 'display: none']) ?>
                                </span>
                            </div>
                            <div class="bd">
                                <p class="misc">
                                    <span><?= Yii::t('module', 'Author') ?>：<?= $module['author'] ?></span>
                                    <span><?= Yii::t('module', 'Version') ?>：<?= $module['version'] ?></span>
                                    <span><?= Yii::t('module', 'Url') ?>：<a href="<?= $module['url'] ?>" target="_blank"><?= $module['url'] ?></a></span>
                                </p>
                                <?php if ($module['description']): ?>
                                    <div class="description">
                                        <div class="inner"><?= $module['description'] ?></div>
                                        <span class="more more-open">&nbsp;</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="notice">暂无待安装模块</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php \app\modules\admin\components\JsBlock::begin() ?>
<script type="text/javascript">
    $(function() {
        var installText = '<?= Yii::t('module', 'Install')?>',
            uninstallText = '<?= Yii::t('module', 'Uninstall')?>';

        $('.install, .uninstall').on('click', function() {
            var $t = $(this);
            if ($t.hasClass('install')) {
                _doInstallUninstall($t);
            } else {
                var droptableMessage = '<?= Config::get('private.dropTableAfterUninstallModule') === true ? '卸载后将同步删除模块相关数据表！！！' : '' ?>';
                layer.confirm('您是否确定卸载"' + $t.parent().parent().find('em').html() + '"模块？' + droptableMessage, {
                    btn: ['确定卸载', '取消'] //按钮
                }, function(index) {
                    _doInstallUninstall($t);
                    layer.close(index);
                }, function() {
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
                beforeSend: function(xhr) {
                    $.fn.lock();
                },
                success: function(response) {
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
                            if ($('#panel-installed').find('ul').length === 0) {
                                $('#panel-installed .notice').remove();
                                $('#panel-installed').append('<ul></ul>');
                            }
                            $tmp.find('.upgrade').show();
                            $('#panel-installed ul').append($tmp);
                            if ($('#panel-notinstalled').find('li').length === 0) {
                                $('#panel-notinstalled').append('<div class="notice">所有模块都已经安装完毕。</div>');
                            }

                            // 更新统计数据
                            $('#tab-installed span').html(parseInt($('#tab-installed span').html()) + 1);
                            $('#tab-not-installed span').html(parseInt($('#tab-not-installed span').html()) - 1);

                            // 添加到控制面板
                            if (response.data) {
                                $('.shortcuts ul:first').append(response.data);
                            }
                        } else {
                            if ($('#panel-notinstalled').find('ul').length === 0) {
                                $('#panel-notinstalled .notice').remove();
                                $('#panel-notinstalled').append('<ul></ul>');
                            }
                            $tmp.find('.upgrade').hide();
                            $('#panel-notinstalled ul').append($tmp);

                            if ($('#panel-installed').find('li').length === 0) {
                                $('#panel-installed').append('<div class="notice">所有模块都已经卸载完毕。</div>');
                            }

                            // 更新统计数据
                            $('#tab-installed span').html(parseInt($('#tab-installed span').html()) - 1);
                            $('#tab-not-installed span').html(parseInt($('#tab-not-installed span').html()) + 1);

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
        $('.upgrade').on('click', function() {
            var $t = $(this);
            $.ajax({
                type: 'POST',
                url: $t.attr('data-url'),
                dataType: 'json',
                beforeSend: function(xhr) {
                    $.fn.lock();
                },
                success: function(response) {
                    if (response.success) {
                        layer.alert('"' + $t.attr('data-name') + '" 模块更新成功。', { icon: 1 });
                    } else {
                        layer.alert(response.error.message);
                    }
                    $.fn.unlock();
                }
            });

            return false;
        });

        // 显示更多按钮操作
        $('.more').on('click', function() {
            var $this = $(this);
            if ($this.hasClass('more-open')) {
                $this.removeClass('more-open').addClass('more-close');
                $this.parent().find('.inner').css({ 'max-height': '100%' });
            } else {
                $this.removeClass('more-close').addClass('more-open');
                $this.parent().find('.inner').css({ 'max-height': '100px' });
            }

            return false;
        });
    });
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
