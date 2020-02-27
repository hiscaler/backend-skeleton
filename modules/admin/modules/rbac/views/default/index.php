<?php

use app\modules\admin\components\JsBlock;
use yii\helpers\Url;

$this->title = '权限控制';
$this->params['breadcrumbs'][] = $this->title;
\app\modules\admin\modules\rbac\assets\AppAsset::register($this);
?>
<div id="rbac-app">
    <div class="rbac-tabs-common">
        <ul>
            <li class="active"><a data-toggle="rbac-users" href="<?= \yii\helpers\Url::toRoute('users') ?>"><?= Yii::t('rbac', 'Users') ?><span class="badges">{{ users.items.length }}</span></a></li>
            <li><a data-toggle="rbac-roles" href="<?= \yii\helpers\Url::toRoute('roles') ?>"><?= Yii::t('rbac', 'Roles') ?><span class="badges">{{ roles.length }}</span></a></li>
            <li><a data-toggle="rbac-permissions" href="<?= \yii\helpers\Url::toRoute('permissions') ?>"><?= Yii::t('rbac', 'Permissions') ?><span class="badges">{{ permissions.length }}</span></a></li>
            <li><a data-toggle="rbac-pending-permissions" href="<?= \yii\helpers\Url::toRoute('default/scan') ?>"><?= Yii::t('rbac', 'Permissions Scan') ?><span class="badges">{{ pendingPermissions.filtered.length }}</span></a></li>
        </ul>
    </div>
    <div id="rbac-panels" class="rbac-grid-view">
        <div id="rbac-users" class="panel">
            <table class="table">
                <thead>
                <tr class="clear-border-top">
                    <th class="serial-number">#</th>
                    <th><?= Yii::t('rbac', 'Username') ?></th>
                    <th v-for="(key, value) in users.extras">{{ value }}</th>
                    <th class="actions last"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in users.items" v-bind:class="{'selected': item.id == activeObject.userId}">
                    <td class="serial-number">{{ item.id }}</td>
                    <td>{{ item.username }}</td>
                    <td v-for="(key, value) in users.extras">{{ item[key] }}</td>
                    <td class="btn-1">
                        <button class="button-rbac" v-on:click="userRolesByUserId(item.id, $index)"><?= Yii::t('rbac', 'Roles') ?></button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="rbac-pop-window" id="window-users" v-show="activeObject.userId">
                <span class="up-arrow"></span>
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= Yii::t('rbac', 'Role Name') ?></th>
                        <th><?= Yii::t('rbac', 'Description') ?></th>
                        <th><?= Yii::t('rbac', 'Rule Name') ?></th>
                        <th><?= Yii::t('rbac', 'Role Data') ?></th>
                        <th class="actions last">
                            <button class="button-rbac button-delete" v-on:click="closeWindow()">X</button>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-bind:class="{active: item.active}" v-for="item in userRoles">
                        <td class="role-name">{{ item.name }}</td>
                        <td>{{ item.description }}</td>
                        <td>{{ item.rule_name }}</td>
                        <td>{{ item.data }}</td>
                        <td class="btn-1">
                            <button class="button-rbac button-delete" v-if="item.active" v-on:click="revoke(item.name, $index)">X</button>
                            <button class="button-rbac" v-else v-on:click="assign(item.name, $index)">+</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="rbac-roles" class="panel" style="display: none;">
            <fieldset class="wrapper">
                <legend>
                    <button class="button-rbac" @click="toggleFormVisible('role')">{{ formVisible.role ? '<?= Yii::t('rbac', 'Hide Form') ?>' : '<?= Yii::t('rbac', 'Show Form') ?>' }}</button>
                </legend>
                <div class="form-rbac" id="rbac-role-form" v-show="formVisible.role">
                    <form action="<?= \yii\helpers\Url::toRoute(['roles/save']) ?>">
                        <div class="row">
                            <label><?= Yii::t('rbac', 'Role Name') ?>:</label><input type="text" class="rbac-input" id="name" name="name" value="" />
                        </div>
                        <div class="row">
                            <label><?= Yii::t('rbac', 'Description') ?>:</label><input type="text" class="rbac-input" id="description" name="description" value="" />
                        </div>
                        <div class="row last-row">
                            <input class="button-rbac" id="rbac-submit-role" type="submit" value="<?= Yii::t('rbac', 'Save') ?>" />
                        </div>
                    </form>
                </div>
            </fieldset>
            <table class="table">
                <thead>
                <tr>
                    <th><?= Yii::t('rbac', 'Role Name') ?></th>
                    <th><?= Yii::t('rbac', 'Description') ?></th>
                    <th><?= Yii::t('rbac', 'Rule Name') ?></th>
                    <th><?= Yii::t('rbac', 'Role Data') ?></th>
                    <th class="actions last"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in roles" v-bind:class="{'selected': item.name == activeObject.role}" v-on:click="roleUpdate($index)">
                    <td class="role-name">{{ item.name }}</td>
                    <td>{{ item.description }}</td>
                    <td>{{ item.rule_name }}</td>
                    <td>{{ item.data }}</td>
                    <td class="btn-3">
                        <button class="button-rbac" v-on:click="roleRemoveChildren(item.name)"><?= Yii::t('rbac', 'Remove Children') ?></button>
                        <button class="button-rbac" v-on:click="roleAddChildren($index, $event)"><?= Yii::t('rbac', 'Add Children') ?></button>
                        <button class="button-rbac" v-on:click="permissionsByRole(item.name, $index)"><?= Yii::t('rbac', 'Permissions') ?></button>
                        <button class="button-rbac button-delete" v-on:click="roleDelete(item.name, $index, $event)">X</button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div id="window-roles" class="rbac-pop-window" v-show="activeObject.role">
                <span class="up-arrow"></span>
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= Yii::t('rbac', 'Role Name') ?></th>
                        <th><?= Yii::t('rbac', 'Description') ?></th>
                        <th><?= Yii::t('rbac', 'Rule Name') ?></th>
                        <th><?= Yii::t('rbac', 'Role Data') ?></th>
                        <th class="actions last">
                            <button class="button-rbac button-delete" v-on:click="closeWindow()">X</button>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-bind:class="{active: item.active}" v-for="item in rolePermissions">
                        <td class="role-name">{{ item.name }}</td>
                        <td>{{ item.description }}</td>
                        <td>{{ item.rule_name }}</td>
                        <td>{{ item.data }}</td>
                        <td class="btn-1">
                            <button class="button-rbac button-delete" v-if="item.active" v-on:click="roleRemoveChild(item.name, $index, $event)">X</button>
                            <button class="button-rbac" v-else v-on:click="roleAddChild(item.name, $index, $event)">+</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="rbac-permissions" class="panel" style="display: none;">
            <fieldset class="wrapper">
                <legend>
                    <button class="button-rbac" @click="toggleFormVisible('permission')">{{ formVisible.permission ? '<?= Yii::t('rbac', 'Hide Form') ?>' : '<?= Yii::t('rbac', 'Show Form') ?>' }}</button>
                </legend>
                <div id="rbac-permission-form" v-show="formVisible.permission">
                    <form class="form-rbac" action="<?= \yii\helpers\Url::toRoute(['permission/create']) ?>">
                        <div class="row">
                            <label><?= Yii::t('rbac', 'Permission Name') ?>:</label><input type="text" class="rbac-input" id="name" name="name" value="" />
                        </div>
                        <div class="row">
                            <label><?= Yii::t('rbac', 'Permission Description') ?>:</label><input type="text" class="rbac-input" id="description" name="description" value="" />
                        </div>
                        <div class="row last-row">
                            <input class="button-rbac" id="rbac-submit-permission" type="submit" value="<?= Yii::t('rbac', 'Save') ?>" />
                        </div>
                    </form>
                </div>
            </fieldset>
            <table class="table">
                <thead>
                <tr>
                    <th><?= Yii::t('rbac', 'Permission Name') ?></th>
                    <th><?= Yii::t('rbac', 'Permission Description') ?></th>
                    <th><?= Yii::t('rbac', 'Rule Name') ?></th>
                    <th><?= Yii::t('rbac', 'Permission Data') ?></th>
                    <th class="actions last"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in permissions">
                    <td class="permission-name">{{ item.name }}</td>
                    <td>{{ item.description }}</td>
                    <td>{{ item.rule_name }}</td>
                    <td>{{ item.data }}</td>
                    <td class="btn-1">
                        <button class="button-rbac button-delete" v-on:click="permissionDelete(item.name, $index, $event)">X</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="rbac-pending-permissions" class="panel" style="display: none;">
            <div class="pending-permissions-search">
                <input v-model.trim="pendingPermissions.keyword" type="text" placeholder="请输入您要搜索的权限名称，回车开始检索" v-on:keyup.enter="pendingPermissionsFilter()" />
            </div>
            <table class="table">
                <thead>
                <tr class="clear-border-top">
                    <th><?= Yii::t('rbac', 'Action') ?></th>
                    <th><?= Yii::t('rbac', 'Permission Description') ?></th>
                    <th class="actions last"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in pendingPermissions.filtered" v-bind:class="{ 'disabled': !item.active, 'enabled': item.active }">
                    <td class="permission-name">{{ item.name }}</td>
                    <td><input type="text" name="description" :disabled="!item.active" :value="item.description" v-model="item.description" /></td>
                    <td class="btn-1">
                        <button class="button-rbac" :disabled="!item.active" @click="permissionSave(item.name, item.description, $index, $event)"><?= Yii::t('rbac', 'Save') ?></button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php JsBlock::begin() ?>
    <script type="text/javascript">
        yadjet.rbac.urls = {
            assign: '<?= Url::toRoute(['users/assign']) ?>',
            revoke: '<?= Url::toRoute(['users/revoke']) ?>',
            users: {
                list: '<?= Url::toRoute(['users/index']) ?>'
            },
            user: {
                roles: '<?= Url::toRoute(['users/roles', 'id' => '_id']) ?>',
                permissions: '<?= Url::toRoute(['users/permissions']) ?>'
            },
            roles: {
                list: '<?= Url::toRoute(['roles/index']) ?>',
                save: '<?= Url::toRoute(['roles/save']) ?>',
                'delete': '<?= Url::toRoute(['roles/delete', 'name' => '_name']) ?>',
                permissions: '<?= Url::toRoute(['roles/permissions-by-role', 'roleName' => '_roleName']) ?>',
                addChild: '<?= Url::toRoute(['roles/add-child', 'roleName' => '_roleName', 'permissionName' => '_permissionName']) ?>',
                addChildren: '<?= Url::toRoute(['roles/add-children', 'roleName' => '_roleName']) ?>',
                removeChild: '<?= Url::toRoute(['roles/remove-child', 'roleName' => '_roleName', 'permissionName' => '_permissionName']) ?>',
                removeChildren: '<?= Url::toRoute(['roles/remove-children', 'name' => '_name']) ?>'
            },
            permissions: {
                list: '<?= Url::toRoute(['permissions/index']) ?>',
                create: '<?= Url::toRoute(['permissions/create']) ?>',
                'delete': '<?= Url::toRoute(['permissions/delete', 'name' => '_name']) ?>',
                scan: '<?= Url::toRoute(['default/scan']) ?>'
            }
        };
        // 获取用户数据
        axios.get(yadjet.rbac.urls.users.list)
            .then(function(response) {
                vm.users.items = response.data.items;
                vm.users.extras = response.data.extras;
            })
            .catch(function(error) {
            });

        axios.get(yadjet.rbac.urls.roles.list)
            .then(function(response) {
                vm.roles = response.data;
            })
            .catch(function(error) {
            });

        axios.get(yadjet.rbac.urls.permissions.list)
            .then(function(response) {
                vm.permissions = response.data;
            })
            .catch(function(error) {
            });

        axios.get(yadjet.rbac.urls.permissions.scan)
            .then(function(response) {
                vm.pendingPermissions = {
                    keyword: null,
                    raw: response.data,
                    filtered: response.data,
                };
            })
            .catch(function(error) {
            });
    </script>
<?php JsBlock::end() ?>