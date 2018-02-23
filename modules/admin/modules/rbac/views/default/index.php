<?php

use yii\helpers\Url;

$this->title = '权限控制';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div id="rbac-app">
        <div class="rbac-tabs-common">
            <ul>
                <li class="active"><a data-toggle="rbac-users" href="<?= \yii\helpers\Url::toRoute('users') ?>"><?= Yii::t('rbac', 'Users') ?><span class="badges">{{ users.items.length }}</span></a></li>
                <li><a data-toggle="rbac-roles" href="<?= \yii\helpers\Url::toRoute('roles') ?>"><?= Yii::t('rbac', 'Roles') ?><span class="badges">{{ roles.length }}</span></a></li>
                <li><a data-toggle="rbac-permissions" href="<?= \yii\helpers\Url::toRoute('permissions') ?>"><?= Yii::t('rbac', 'Permissions') ?><span class="badges">{{ permissions.length }}</span></a></li>
                <li><a data-toggle="rbac-pending-permissions" href="<?= \yii\helpers\Url::toRoute('default/scan') ?>"><?= Yii::t('rbac', 'Permissions Scan') ?><span class="badges">{{ pendingPermissions.length }}</span></a></li>
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
                <div id="rbac-pop-window" v-show="activeObject.userId">
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
                        <tr v-for="item in userRoles">
                            <td class="role-name">{{ item.name }}</td>
                            <td>{{ item.description }}</td>
                            <td>{{ item.rule_name }}</td>
                            <td>{{ item.data }}</td>
                            <td class="btn-1">
                                <button class="button-rbac" v-show="!item.active" v-on:click="assign(item.name, $index)">+</button>
                                <button class="button-rbac" v-show="item.active" v-on:click="revoke(item.name, $index)">X</button>
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
                        <form action="<?= \yii\helpers\Url::toRoute(['roles/create']) ?>">
                            <div class="row">
                                <label><?= Yii::t('rbac', 'Role Name') ?>:</label><input type="text" class="rbac-input" id="name" name="name" value="" />
                            </div>
                            <div class="row">
                                <label><?= Yii::t('rbac', 'Description') ?>:</label><input type="text" class="rbac-input" id="description" name="description" value="" />
                            </div>
                            <div class="row last-row">
                                <input class="button-rbac" id="rbac-sumbit-role" type="submit" value="<?= Yii::t('rbac', 'Save') ?>" />
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
                    <tr v-for="item in roles" v-bind:class="{'selected': item.name == activeObject.role}">
                        <td class="role-name">{{ item.name }}</td>
                        <td>{{ item.description }}</td>
                        <td>{{ item.rule_name }}</td>
                        <td>{{ item.data }}</td>
                        <td class="btn-3">
                            <button class="button-rbac" v-on:click="roleDelete(item.name, $index, $event)">X</button>
                            <button class="button-rbac" v-on:click="roleRemoveChildren(item.name)"><?= Yii::t('rbac', 'Remove Children') ?></button>
                            <button class="button-rbac" v-on:click="permissionsByRole(item.name, $index)"><?= Yii::t('rbac', 'Permissions') ?></button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div id="rbac-permissions-by-role" v-show="activeObject.role">
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
                        <tr v-for="item in rolePermissions">
                            <td class="role-name">{{ item.name }}</td>
                            <td>{{ item.description }}</td>
                            <td>{{ item.rule_name }}</td>
                            <td>{{ item.data }}</td>
                            <td class="btn-1">
                                <button class="button-rbac" v-show="!item.active" v-on:click="roleAddChild(item.name, $index, $event)">+</button>
                                <button class="button-rbac" v-show="item.active" v-on:click="roleRemoveChild(item.name, $index, $event)">X</button>
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
                    <div id="rbac-persmission-form" v-show="formVisible.permission">
                        <form class="form-rbac" action="<?= \yii\helpers\Url::toRoute(['permission/create']) ?>">
                            <div class="row">
                                <label><?= Yii::t('rbac', 'Permission Name') ?>:</label><input type="text" class="rbac-input" id="name" name="name" value="" />
                            </div>
                            <div class="row">
                                <label><?= Yii::t('rbac', 'Permission Description') ?>:</label><input type="text" class="rbac-input" id="description" name="description" value="" />
                            </div>
                            <div class="row last-row">
                                <input class="button-rbac" id="rbac-sumbit-permission" type="submit" value="<?= Yii::t('rbac', 'Save') ?>" />
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
                            <button class="button-rbac" v-on:click="permissionDelete(item.name, $index, $event)">X</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="rbac-pending-permissions" class="panel" style="display: none;">
                <table class="table">
                    <thead>
                    <tr class="clear-border-top">
                        <th><?= Yii::t('rbac', 'Action') ?></th>
                        <th><?= Yii::t('rbac', 'Permission Description') ?></th>
                        <th class="actions last"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in pendingPermissions" v-bind:class="{ 'disabled': !item.active, 'enabled': item.active }">
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
<?php \app\modules\admin\components\CssBlock::begin() ?>
    <style type="text/css">
        body {
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }

        fieldset, input {
            border: 1px #E9E9E9 solid;
        }

        fieldset {
            border: none;
            border-top: 1px #E9E9E9 solid;
            margin-top: 10px;
        }

        input {
            padding: 6px;
            border-radius: 2px;
        }

        #rbac-app {
        }

        .rbac-tabs-common {
            height: 25px;
            margin: 0;
        }

        .rbac-tabs-common li {
            float: left;
            list-style: none outside none;
            margin: 0 3px;
        }

        .rbac-tabs-common li.active a {
            background: none repeat scroll 0 0 #F5F5F5;
            border-top: #DD4B39 solid 2px;
            border-bottom: 1px solid #F8F8F8;
            color: #4362BF;
        }

        .rbac-tabs-common a:hover {
            background: none repeat scroll 0 0 #F5F5F5;
        }

        .rbac-tabs-common a {
            -moz-border-bottom-colors: none;
            -moz-border-image: none;
            -moz-border-left-colors: none;
            -moz-border-right-colors: none;
            -moz-border-top-colors: none;
            background: none repeat scroll 0 0 #FFF;
            border-color: #E9E9E9;
            border-style: solid solid none;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            border-width: 1px 1px medium;
            color: #666677;
            display: inline-block;
            padding: 2px 15px;
            text-decoration: none;
        }

        .rbac-tabs-common li.active a {
            padding-top: 1px;
        }

        .rbac-tabs-common .badges {
            position: absolute;
            margin-top: -10px;
            display: inline;
            background-color: #ac2925;
            color: #FFFFFF;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            font-size: 10px;
        }

        #rbac-panels {
            border: #E9E9E9 solid 1px;
            border-radius: 0 0 6px 6px;
        }

        /*******************************************************************************
         * Common
         ******************************************************************************/
        .button-rbac {
            border: #E9E9E9 solid 1px;
            color: #7d8389;
            background-color: #FFF;
            border-radius: 3px;
            cursor: pointer;
        }

        .wrapper {
            margin-bottom: 10px;
        }

        /*******************************************************************************
         * Form
         ******************************************************************************/
        .form-rbac {
        }

        .form-rbac div.row {
            padding: 10px 0;
            border-bottom: 1px #E9E9E9 solid;
        }

        .form-rbac div.last-row {
            border-bottom: none;
        }

        .form-rbac div.row label {
            display: inline-block;
            width: 100px;
            text-align: right;
            margin-right: 10px;
        }

        .form-rbac div.row input.rbac-input {
            width: 300px;
        }

        .form-rbac div.last-row input.button-rbac {
            margin-left: 110px;
            padding: 3px 14px;
            background-color: #F5F5F5;
            color: #4362BF;
        }

        /*******************************************************************************
         * Grid View
         ******************************************************************************/
        .rbac-grid-view-loading {
            background: url(/admin/images/loading.gif) no-repeat;
        }

        .rbac-grid-view {
            width: 100%;
            overflow: auto;
        }

        .rbac-grid-view table.table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .rbac-grid-view table.table th, .rbac-grid-view table.table td {
            border-bottom: 1px #E9E9E9 solid;
            padding: 6px;
            /* 保持不换行 */
            text-overflow: ellipsis;
            overflow: hidden;
            word-break: keep-all; /* for ie */
            white-space: nowrap; /* for chrome */
        }

        .rbac-grid-view table.table th {
            padding-left: 10px !important;
        }

        .rbac-grid-view table.table th.nowrap, .rbac-grid-view table.table td.nowrap {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .rbac-grid-view table.table th.pointer, .rbac-grid-view table.table td.pointer {
            cursor: pointer;
        }

        .rbac-grid-view table.table th {
            border-top: 1px #E9E9E9 solid;
            background: #F5F5F5 url(/admin/images/grid-view-header-bg.gif) no-repeat right center;
            padding: 5px 4px;
            color: #7d8389;
            text-align: left;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            font-weight: normal;
        }

        .rbac-grid-view table.table tr.clear-border-top th {
            border-top: none;
        }

        .rbac-grid-view table.table th.last {
            background: #F5F5F5;
        }

        .rbac-grid-view table.table th a {
            color: #7d8389;
            font-weight: bold;
            text-decoration: none;
        }

        .rbac-grid-view table.table th a:hover {
            color: #3b5998;
        }

        .rbac-grid-view table.table th a.asc {
            background: url(/admin/images/up.gif) right center no-repeat;
            padding-right: 10px;
        }

        .rbac-grid-view table.table th a.desc {
            background: url(/admin/images/down.gif) right center no-repeat;
            padding-right: 10px;
        }

        .rbac-grid-view table.table th.serial-number {
            text-align: center;
            padding: 0 !important;
            width: 30px;
        }

        .rbac-grid-view table.table tr.even {
            background: #FFF;
        }

        .rbac-grid-view table.table tr.odd {
            background: #FAFCFF;
        }

        .rbac-grid-view table.table tr.block {
            background: #FFF6BF;
        }

        .rbac-grid-view table.table tr.selected td {
            background: #FFFFEF;
        }

        .rbac-grid-view table.table tr.disabled td,
        .rbac-grid-view table.table tr.disabled td input,
        .rbac-grid-view table.table tr.disabled td button {
            color: #CCC;
        }

        .rbac-grid-view table.table tr:hover {
            background: #ECFBD4;
        }

        .rbac-grid-view table.table tr td img {
            border: none;
            vertical-align: middle;
        }

        .rbac-grid-view .link-column img {
            border: 0;
        }

        .rbac-grid-view .button-column {
            text-align: center;
        }

        .rbac-grid-view .button-column img {
            border: 0;
        }

        .rbac-grid-view .checkbox-column {
            width: 15px;
        }

        .rbac-grid-view .summary {
            margin: 0 0 5px 0;
            line-height: 31px;
            padding-right: 10px;
            text-align: right;
            color: #7d8389;
        }

        .rbac-grid-view .summary b {
            margin: 0 5px;
        }

        .rbac-grid-view .pager {
        }

        .rbac-grid-view .empty {
            background: #FFFCC9 url(/admin/images/notice.png) no-repeat 5px center;
            padding: 2px 5px 2px 25px;
            height: 24px;
            line-height: 24px;
        }

        .rbac-grid-view tr.filters td {
            padding: 2px 0 2px 5px;
            margin: 0;
        }

        .rbac-grid-view .filters input {
            border: 1px solid #E9E9E9;
            padding: 4px 5px;
        }

        .rbac-grid-view table.table td.center {
            text-align: center;
        }

        .rbac-grid-view table.table td.pk {
            width: 50px;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #999;
        }

        .rbac-grid-view table.table td.checkbox {
            width: 20px;
            text-align: center;
        }

        .rbac-grid-view table.table td.serial-number {
            width: 30px;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #999;
            text-align: center;
        }

        .rbac-grid-view table.table td.pk a {
            color: #999;
        }

        .rbac-grid-view table.table td.date {
            width: 65px;
            text-align: center;
        }

        .rbac-grid-view table.table td.datetime {
            width: 160px;
            text-align: center;
        }

        .rbac-grid-view table.table td.username {
            width: 60px;
            text-align: center;
        }

        .rbac-grid-view table.table td.user-role {
            width: 120px;
            text-align: center;
        }

        .rbac-grid-view table.table td.date-time-ago {
            width: 85px;
            text-align: center;
        }

        .rbac-grid-view table.table td.ordering {
            width: 40px;
            text-align: center;
        }

        .rbac-grid-view table.table td.icon {
            width: 34px;
            text-align: center;
        }

        .rbac-grid-view table.table td.boolean {
            width: 30px;
            text-align: center;
        }

        .rbac-grid-view table.table td.role-name {
            width: 120px;
        }

        .rbac-grid-view table.table td.permission-name {
            width: 320px;
        }

        .rbac-grid-view table.table th.actions, .rbac-grid-view table.table td.actions {
            text-align: center;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rbac-grid-view table.table td.btn-1 {
            width: 20px;
            _width: 30px;
        }

        .rbac-grid-view table.table td.btn-2 {
            width: 40px;
            _width: 40px;
        }

        .rbac-grid-view table.table td.btn-3 {
            width: 60px;
            _width: 70px;
        }

        .rbac-grid-view table.table td.btn-4 {
            width: 80px;
            _widht: 90px;
        }

        .rbac-grid-view table.table td.btn-5 {
            width: 100px;
            _widht: 110px;
        }

        #rbac-pending-permissions table input {
            width: 96%;
        }
        #rbac-pop-window {
            border: #E9E9E9 solid 1px;
            border-radius: 0 0 6px 6px;
        }
    </style>
<?php \app\modules\admin\components\CssBlock::end() ?>
<?php \app\modules\admin\components\JsBlock::begin() ?>
    <script type="text/javascript">
        yadjet.rbac = yadjet.rbac || {};
        yadjet.rbac.debug = yadjet.rbac.debug || true;
        yadjet.rbac.urls = yadjet.rbac.urls || {
            assign: undefined,
            revoke: undefined,
            users: {
                list: undefined
            },
            user: {
                roles: undefined,
                permissions: undefined
            },
            roles: {
                list: undefined, // 角色列表
                create: undefined, // 添加角色
                read: undefined, // 查看角色
                update: undefined, // 更新角色
                'delete': undefined, // 删除角色
                permissions: undefined, // 角色对应的权限
                addChild: undefined, // 角色关联权限操作
                removeChild: undefined, // 删除角色中的某个关联权限
                removeChildren: undefined, // 删除角色关联的所有权限
            },
            permissions: {
                create: undefined,
                read: undefined,
                update: undefined,
                'delete': undefined,
                scan: undefined
            }
        };

        axios.interceptors.request.use(function (config) {
            $.fn.lock();
            return config;
        }, function (error) {
            $.fn.unlock();
            return Promise.reject(error);
        });

        axios.interceptors.response.use(function (response) {
            $.fn.unlock();
            return response;
        }, function (error) {
            $.fn.unlock();
            return Promise.reject(error);
        });

        var vm = new Vue({
            el: '#rbac-app',
            data: {
                activeObject: {
                    userId: 0,
                    role: undefined
                },
                users: {
                    items: {},
                    extras: {}
                },
                user: {
                    roles: {},
                    permissions: {}
                },
                roles: [],
                role: {
                    permissions: {}
                },
                permissions: [],
                pendingPermissions: {},
                formVisible: {
                    role: false,
                    permission: false
                }
            },
            methods: {
                isEmptyObject: function (e) {
                    var t;
                    for (t in e)
                        return !1;
                    return !0
                },
                userRolesByUserId: function (userId, index) {
                    axios.get(yadjet.rbac.urls.user.roles.replace('_id', userId))
                        .then(function (response) {
                            vm.user.roles = response.data;
                            vm.activeObject.userId = userId;
                            var $tr = $('#rbac-users > table tr:eq(' + (index + 1) + ')');
                            var offset = $tr.offset();
                            $('#rbac-pop-window').css({
                                position: 'absolute',
                                left: offset.left + 40,
                                top: offset.top + $tr.find('td').outerHeight()
                            });
                        })
                        .catch(function (error) {
                            vm.user.roles = [];
                            vm.activeObject.userId = undefined;
                        });
                },
                // 给用户授权
                assign: function (roleName, index) {
                    axios.post(yadjet.rbac.urls.assign, {roleName: roleName, userId: vm.activeObject.userId})
                        .then(function (response) {
                            vm.user.roles.push(vm.roles[index]);
                        })
                        .catch(function (error) {
                        });
                },
                // 撤销用户授权
                revoke: function (roleName, index) {
                    axios.post(yadjet.rbac.urls.revoke, {roleName: roleName, userId: vm.activeObject.userId})
                        .then(function (response) {
                            for (var i in vm.user.roles) {
                                console.info(vm.user.roles[i].name);
                                if (vm.user.roles[i].name === roleName) {
                                    vm.user.roles.splice(i, 1);
                                    break;
                                }
                            }
                        })
                        .catch(function (error) {
                        });
                },
                // 删除角色
                roleDelete: function (roleName, index, event) {
                    layer.confirm('确定删除该角色？', {icon: 3, title: '提示'}, function (boxIndex) {
                        axios.post(yadjet.rbac.urls.roles.delete.replace('_name', roleName))
                            .then(function (response) {
                                vm.roles.splice(index, 1);
                            })
                            .catch(function (error) {
                            });

                        layer.close(boxIndex);
                    });
                },
                // 删除角色关联的所有权限
                roleRemoveChildren: function (roleName) {
                    layer.confirm('删除该角色关联的所有权限？', {icon: 3, title: '提示'}, function (boxIndex) {
                        axios.post(yadjet.rbac.urls.roles.removeChildren.replace('_name', roleName))
                            .then(function (response) {
                                vm.role.permissions = [];
                            })
                            .catch(function (error) {
                            });

                        layer.close(boxIndex);
                    });
                },
                // 根据角色获取关联的所有权限
                permissionsByRole: function (roleName, index) {
                    axios.get(yadjet.rbac.urls.roles.permissions.replace('_roleName', roleName))
                        .then(function (response) {
                            vm.activeObject.role = roleName;
                            vm.role.permissions = response.data;
                        })
                        .catch(function (error) {
                        });
                },
                // 分配权限给角色
                roleAddChild: function (permissionName, index, event) {
                    axios.post(yadjet.rbac.urls.roles.addChild.replace('_roleName', vm.activeObject.role).replace('_permissionName', permissionName))
                        .then(function (response) {
                            for (var i in vm.permissions) {
                                if (vm.permissions[i].name == permissionName) {
                                    vm.role.permissions.push(vm.permissions[i]);
                                    break;
                                }
                            }
                        })
                        .catch(function (error) {
                        });
                },
                // 从角色中移除权限
                roleRemoveChild: function (permissionName, index, event) {
                    layer.confirm('确定删除该权限？', {icon: 3, title: '提示'}, function (boxIndex) {
                        axios.post(yadjet.rbac.urls.roles.removeChild.replace('_roleName', vm.activeObject.role).replace('_permissionName', permissionName))
                            .then(function (response) {
                                for (var i in vm.role.permissions) {
                                    if (vm.role.permissions[i].name == permissionName) {
                                        vm.role.permissions.splice(i, 1);
                                        break;
                                    }
                                }
                            })
                            .catch(function (error) {
                            });

                        layer.close(boxIndex);
                    });
                },
                // 切换添加表单是否可见
                toggleFormVisible: function (formName) {
                    vm.formVisible[formName] = !vm.formVisible[formName];
                },
                // 保存扫描的权限
                permissionSave: function (name, description, index, event) {
                    axios.post(yadjet.rbac.urls.permissions.create, {name: name, description: description})
                        .then(function (response) {
                            if (response.data.success) {
                                vm.permissions.push(response.data.data);
                                vm.pendingPermissions[index].active = false;
                            }
                        })
                        .catch(function (error) {
                        });
                },
                // 删除单个权限
                permissionDelete: function (name, index, event) {
                    layer.confirm('确定删除该权限？', {icon: 3, title: '提示'}, function (boxIndex) {
                        axios.post(yadjet.rbac.urls.permissions.delete.replace('_name', name))
                            .then(function (response) {
                                vm.permissions.splice(index, 1);
                                for (var i in vm.pendingPermissions) {
                                    if (vm.pendingPermissions[i].name == name) {
                                        vm.pendingPermissions[i].active = true;
                                        break;
                                    }
                                }
                            })
                            .catch(function (error) {
                            });

                        layer.close(boxIndex);
                    });
                }
            },
            computed: {
                // 当前用户的角色
                userRoles: function () {
                    var roles = [], role;
                    for (var i in this.roles) {
                        role = clone(this.roles[i]);
                        role.active = false;
                        for (var j in vm.user.roles) {
                            if (role.name == this.user.roles[j].name) {
                                role.active = true;
                                break;
                            }
                        }
                        roles.push(role);
                    }

                    return roles;
                },
                // 当前操作角色关联的权限
                rolePermissions: function () {
                    var permissions = [], permission;
                    for (var i in this.permissions) {
                        permission = clone(this.permissions[i]);
                        permission.active = false;
                        for (var j in this.role.permissions) {
                            if (permission.name == this.role.permissions[j].name) {
                                permission.active = true;
                                break;
                            }
                        }
                        permissions.push(permission);
                    }

                    return permissions;
                }
            }
        });

        $(function () {
            $('.rbac-tabs-common li a').on('click', function () {
                var $t = $(this);
                $t.parent().addClass('active').siblings().removeClass('active');
                $('#rbac-app .panel').hide();
                $('#rbac-app #' + $t.attr('data-toggle')).show();

                return false;
            });

            $('#rbac-sumbit-role').on('click', function () {
                $.ajax({
                    type: 'POST',
                    url: yadjet.rbac.urls.roles.create,
                    data: $('#rbac-role-form form').serialize(),
                    returnType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // vm.roles[response.data.name] = response.data;
                            vm.roles.push(response.data);
                        } else {
                            layer.alert(response.error.message);
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        layer.alert('ERROR ' + XMLHttpRequest.status + ' 错误信息： ' + XMLHttpRequest.responseText);
                    }
                });

                return false;
            });

            $('#rbac-sumbit-permission').on('click', function () {
                $.ajax({
                    type: 'POST',
                    url: yadjet.rbac.urls.permissions.create,
                    data: $('#rbac-persmission-form form').serialize(),
                    returnType: 'json',
                    success: function (response) {
                        if (response.success) {
                            vm.permissions.push(response.data);
                        } else {
                            layer.alert(response.error.message);
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        layer.alert('ERROR ' + XMLHttpRequest.status + ' 错误信息： ' + XMLHttpRequest.responseText);
                    }
                });

                return false;
            });
        });
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
                create: '<?= Url::toRoute(['roles/create']) ?>',
                'delete': '<?= Url::toRoute(['roles/delete', 'name' => '_name']) ?>',
                permissions: '<?= Url::toRoute(['roles/permissions-by-role', 'roleName' => '_roleName']) ?>',
                addChild: '<?= Url::toRoute(['roles/add-child', 'roleName' => '_roleName', 'permissionName' => '_permissionName']) ?>',
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
            .then(function (response) {
                vm.users.items = response.data.items;
                vm.users.extras = response.data.extras;
            })
            .catch(function (error) {
            });

        axios.get(yadjet.rbac.urls.roles.list)
            .then(function (response) {
                vm.roles = response.data;
            })
            .catch(function (error) {
            });

        axios.get(yadjet.rbac.urls.permissions.list)
            .then(function (response) {
                vm.permissions = response.data;
            })
            .catch(function (error) {
            });

        axios.get(yadjet.rbac.urls.permissions.scan)
            .then(function (response) {
                vm.pendingPermissions = response.data;
            })
            .catch(function (error) {
            });
    </script>
<?php \app\modules\admin\components\JsBlock::end() ?>