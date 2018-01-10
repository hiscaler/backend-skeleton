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
                            <?= $module['name'] ?>
                            <span class="icon"><?= Html::img($module['icon'], ['src' => $module['name']]) ?></span>
                            <span class="buttons"><?= Html::a(Yii::t('module', 'Uninstall'), ['uninstall', 'alias' => $module['alias']], ['class' => 'uninstall', 'data-key' => $module['alias'], 'data-url' => \yii\helpers\Url::toRoute(['install', 'alias' => $module['alias']])]) ?></span>
                        </div>
                        <div class="bd">
                            <p class="misc">
                                <span><?= Yii::t('module', 'Author') ?>：<?= $module['author'] ?></span>
                                <span><?= Yii::t('module', 'Version') ?>：<?= $module['version'] ?></span>
                                <span><?= Yii::t('module', 'Url') ?>：<?= $module['url'] ?></span>
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
                            <?= $module['name'] ?>
                            <span class="icon"><?= Html::img($module['icon'], ['src' => $module['name']]) ?></span>
                            <span class="buttons"><?= Html::a(Yii::t('module', 'Install'), ['install', 'alias' => $module['alias']], ['class' => 'install', 'data-key' => $module['alias'], 'data-url' => \yii\helpers\Url::toRoute(['uninstall', 'alias' => $module['alias']])]) ?></span>
                        </div>
                        <div class="bd">
                            <p class="misc">
                                <span><?= Yii::t('module', 'Author') ?>：<?= $module['author'] ?></span>
                                <span><?= Yii::t('module', 'Version') ?>：<?= $module['version'] ?></span>
                                <span><?= Yii::t('module', 'Url') ?>：<?= $module['url'] ?></span>
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
            var $t = $(this), url = $t.attr('href');
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
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
                        $(isInstall ? '#panel-installed ul' : '#panel-notinstalled ul').append($tmp);
                    } else {
                        layer.alert(response.error.message);
                    }
                }
            });

            return false;
        });
    });
</script>
<?php \app\modules\admin\components\JsBlock::end() ?>
