<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\DetailView;

$formatter = Yii::$app->getFormatter();
$tab = Yii::$app->getRequest()->get('tab', 'detail');
?>

<div class="clearfix">
    <ul id="tenant-tabs" class="tabs-common">
        <?php
        foreach ($items as $item):
            $cssClass = "panel-tenant-{$tab}" == $item['id'] ? ' class="active"' : '';
            ?>
            <li<?php echo $cssClass; ?>><?php echo Html::a($item['label'], $item['url'], ['data-toggle' => $item['id']]); ?></li>
        <?php endforeach; ?>
    </ul>
    <div class="panels">
        <div id="panel-tenant-detail" class="tenant-view tab-pane"
             style="<?= $tab == 'detail' ? '' : 'display: none' ?>">

            <?=
            DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'key',
                    'name',
                    'domain_name',
                    [
                        'attribute' => 'language',
                        'value' => Yii::t('language', $model['language']),
                    ],
                    [
                        'attribute' => 'timezone',
                        'value' => Yii::t('timezone', $model['timezone']),
                    ],
                    'date_format',
                    'time_format',
                    'datetime_format',
                    'enabled:boolean',
                    'description',
                    [
                        'attribute' => 'created_by',
                        'value' => $model['creater']['nickname']
                    ],
                    'created_at:datetime',
                    [
                        'attribute' => 'updated_by',
                        'value' => $model['updater']['nickname']
                    ],
                    'updated_at:datetime',
                ],
            ])
            ?>

        </div>

        <div id="panel-tenant-modules" class="tenant-view tab-pane"
             style="<?= $tab == 'modules' ? '' : 'display: none' ?>">
            <div class="clearfix">
                <ul class="list clearfix">
                    <?php foreach ($model['modules'] as $moduleName): ?>
                        <li><?= Yii::t('model', Inflector::camel2words(str_replace('app-models-', '', $moduleName))) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div id="panel-tenant-users" class="tenant-view tab-pane" style="<?= $tab == 'users' ? '' : 'display: none' ?>">
            <div class="grid-view clearfix">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?= Yii::t('user', 'Username') ?></th>
                        <th><?= Yii::t('user', 'Nickname') ?></th>
                        <th><?= Yii::t('user', 'Email') ?></th>
                        <th><?= Yii::t('user', 'Group') ?></th>
                        <th><?= Yii::t('user', 'Role') ?></th>
                        <th><?= Yii::t('user', 'Rule') ?></th>
                        <th><?= Yii::t('app', 'Enabled') ?></th>
                        <th class="data-status last"><?= Yii::t('app', 'Status') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($model['users'] as $i => $user): ?>
                        <tr>
                            <td class="serial-number"><?= $i + 1 ?></td>
                            <td class="username"><?= $user['username'] ?></td>
                            <td class="username"><?= $user['nickname'] ?></td>
                            <td class="email"><?= $user['email'] ?></td>
                            <td class="user-group-name"><?= $user['group_name'] ?></td>
                            <td class="user-role"><?php // $formatter->asUserRole($user['role'])    ?></td>
                            <td class="rule"><?php // $user['rule_name']    ?></td>
                            <td class="boolean"><?= $formatter->asBoolean($user['enabled']) ?></td>
                            <td class="data-status"><?php // $formatter->asUserStatus($user['status'])    ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="panel-tenant-access-tokens" class="tenant-view tab-pane"
             style="<?= $tab == 'access-tokens' ? '' : 'display: none' ?>">
            <div class="grid-view clearfix">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?= Yii::t('tenantAccessToken', 'Title') ?></th>
                        <th><?= Yii::t('tenantAccessToken', 'Access Token') ?></th>
                        <th><?= Yii::t('app', 'Status') ?></th>
                        <th class="last"><?= Yii::t('app', 'Enabled') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($model['accessTokens'] as $i => $accessToken): ?>
                        <tr>
                            <td class="serial-number"><?= $i + 1 ?></td>
                            <td><?= $accessToken['title'] ?></td>
                            <td class="access-token"><?= $accessToken['access_token'] ?></td>
                            <td class="data-status"><?= $formatter->asDataStatus($accessToken['status']) ?></td>
                            <td class="boolean"><?= $formatter->asBoolean($accessToken['enabled']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
