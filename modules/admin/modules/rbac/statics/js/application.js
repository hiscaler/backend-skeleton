/**
 * RBAC module javascript
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
var yadjet = yadjet || {};
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
        save: undefined, // 添加、更新角色
        read: undefined, // 查看角色
        update: undefined, // 更新角色
        'delete': undefined, // 删除角色
        permissions: undefined, // 角色对应的权限
        addChild: undefined, // 角色关联权限操作
        addChildren: undefined, // 添加所有权限至指定的角色
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

axios.interceptors.request.use(function(config) {
    $.fn.lock();
    return config;
}, function(error) {
    $.fn.unlock();
    return Promise.reject(error);
});

axios.interceptors.response.use(function(response) {
    $.fn.unlock();
    return response;
}, function(error) {
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
            keyword: null,
            permissions: {}
        },
        permissions: {
            keyword: null,
            raw: [],
            filtered: [],
        },
        pendingPermissions: {
            keyword: null,
            raw: [],
            filtered: [],
        },
        pendingPermissionName: null,
        formVisible: {
            role: false,
            permission: false
        },
        window: {
            rolePermissions: false
        }
    },
    methods: {
        isEmptyObject: function(e) {
            var t;
            for (t in e)
                return !1;
            return !0
        },
        userRolesByUserId: function(userId, index) {
            axios.get(yadjet.rbac.urls.user.roles.replace('_id', userId))
                .then(function(response) {
                    vm.user.roles = response.data;
                    vm.activeObject.userId = userId;
                    var $tr = $('#rbac-users > table tr:eq(' + (index + 1) + ')'),
                        offset = $tr.offset();
                    $('#window-users').css({
                        position: 'absolute',
                        left: offset.left + 40,
                        top: offset.top + $tr.find('td').outerHeight()
                    });
                })
                .catch(function(error) {
                    vm.user.roles = [];
                    vm.activeObject.userId = undefined;
                });
        },
        // 给用户授权
        assign: function(roleName, index) {
            axios.post(yadjet.rbac.urls.assign, { roleName: roleName, userId: vm.activeObject.userId })
                .then(function(response) {
                    vm.user.roles.push(vm.roles[index]);
                    var items = vm.users.items;
                    for (var i in items) {
                        console.info(items[i]);
                        if (items[i].id == vm.activeObject.userId) {
                            vm.users.items[i].roles.push(roleName);
                        }
                    }
                })
                .catch(function(error) {
                });
        },
        // 撤销用户授权
        revoke: function(roleName, index) {
            axios.post(yadjet.rbac.urls.revoke, { roleName: roleName, userId: vm.activeObject.userId })
                .then(function(response) {
                    var items = vm.user.roles;
                    for (var i in items) {
                        if (items[i].name === roleName) {
                            vm.user.roles.splice(i, 1);
                            break;
                        }
                    }
                    items = vm.users.items;
                    for (var i in items) {
                        if (items[i].id == vm.activeObject.userId) {
                            for (var j in items[i].roles) {
                                if (items[i].roles[j] === roleName) {
                                    vm.users.items[i].roles.splice(j, 1);
                                }
                            }
                            break;
                        }
                    }
                })
                .catch(function(error) {
                });
        },
        // 更新角色
        roleUpdate: function(key) {
            var role = vm.roles[key];
            $('#rbac-role-form input#role-name').val(role.name);
            $('#rbac-role-form input#role-description').val(role.description);
            vm.formVisible.role = true;
        },
        // 删除角色
        roleDelete: function(roleName, index, event) {
            layer.confirm('确定删除该角色？', { icon: 3, title: '提示' }, function(boxIndex) {
                axios.post(yadjet.rbac.urls.roles.delete.replace('_name', roleName))
                    .then(function(response) {
                        vm.roles.splice(index, 1);
                    })
                    .catch(function(error) {
                    });

                layer.close(boxIndex);
            });
        },
        // 删除角色关联的所有权限
        roleRemoveChildren: function(roleName) {
            layer.confirm('删除该角色关联的所有权限？', { icon: 3, title: '提示' }, function(boxIndex) {
                vm.activeObject.role = roleName;
                vm.window.rolePermissions = false;
                axios.post(yadjet.rbac.urls.roles.removeChildren.replace('_name', roleName))
                    .then(function(response) {
                        vm.role.permissions = [];
                    })
                    .catch(function(error) {
                    });

                layer.close(boxIndex);
            });
        },
        permissionsFilter: function() {
            var keyword = vm.permissions.keyword;
            if (keyword) {
                var items = vm.permissions.raw.filter(function(item) {
                    return item.name.indexOf(keyword) !== -1;
                });
                vm.permissions = {
                    ...vm.permissions,
                    ...{
                        filtered: items,
                    }
                }
            } else {
                vm.permissions.filtered = vm.permissions.raw;
            }
        },
        pendingPermissionsFilter: function() {
            var keyword = vm.pendingPermissions.keyword;
            if (keyword) {
                var items = vm.pendingPermissions.raw.filter(function(item) {
                    return item.name.indexOf(keyword) !== -1;
                });
                vm.pendingPermissions = {
                    ...vm.pendingPermissions,
                    ...{
                        filtered: items,
                    }
                }
            } else {
                vm.pendingPermissions.filtered = vm.pendingPermissions.raw;
            }
        },
        // 根据角色获取关联的所有权限
        permissionsByRole: function(roleName, index) {
            vm.window.rolePermissions = true;
            axios.get(yadjet.rbac.urls.roles.permissions.replace('_roleName', roleName))
                .then(function(response) {
                    vm.activeObject.role = roleName;
                    vm.role.permissions = response.data;

                    var $tr = $('#rbac-roles > table tr:eq(' + (index + 1) + ')'),
                        offset = $tr.offset();
                    $('#window-roles').css({
                        position: 'absolute',
                        left: offset.left + 40,
                        top: offset.top + $tr.find('td').outerHeight()
                    });
                })
                .catch(function(error) {
                });
        },
        // 分配权限给角色
        roleAddChild: function(permissionName, index, event) {
            axios.post(yadjet.rbac.urls.roles.addChild.replace('_roleName', vm.activeObject.role).replace('_permissionName', permissionName))
                .then(function(response) {
                    for (var i in vm.permissions.raw) {
                        if (vm.permissions.raw[i].name == permissionName) {
                            vm.role.permissions.push(vm.permissions.raw[i]);
                            break;
                        }
                    }
                })
                .catch(function(error) {
                });
        },
        // 添加所有权限至指定的角色
        roleAddChildren: function(roleName) {
            layer.confirm('确定授予该角色所有权限？', { icon: 3, title: '提示' }, function(boxIndex) {
                vm.activeObject.role = roleName;
                vm.window.rolePermissions = false;
                axios.post(yadjet.rbac.urls.roles.addChildren.replace('_roleName', roleName))
                    .then(function(response) {
                        for (var i in vm.permissions.raw) {
                            vm.role.permissions.push(vm.permissions.raw[i]);
                        }
                    })
                    .catch(function(error) {
                    });

                layer.close(boxIndex);
            });
        },
        // 从角色中移除权限
        roleRemoveChild: function(permissionName, index, event) {
            layer.confirm('确定删除该权限？', { icon: 3, title: '提示' }, function(boxIndex) {
                axios.post(yadjet.rbac.urls.roles.removeChild.replace('_roleName', vm.activeObject.role).replace('_permissionName', permissionName))
                    .then(function(response) {
                        for (var i in vm.role.permissions) {
                            if (vm.role.permissions[i].name == permissionName) {
                                console.info('delete');
                                vm.role.permissions.splice(i, 1);
                                break;
                            }
                        }
                    })
                    .catch(function(error) {
                    });

                layer.close(boxIndex);
            });
        },
        // 切换添加表单是否可见
        toggleFormVisible: function(formName) {
            vm.formVisible[formName] = !vm.formVisible[formName];
        },
        // 保存扫描的权限
        permissionSave: function(name, description, index, event) {
            axios.post(yadjet.rbac.urls.permissions.create, { name: name, description: description })
                .then(function(response) {
                    if (response.data.success) {
                        vm.permissions.push(response.data.data);
                        vm.pendingPermissions[index].active = false;
                    }
                })
                .catch(function(error) {
                });
        },
        // 删除单个权限
        permissionDelete: function(name, index, event) {
            layer.confirm('确定删除该权限？', { icon: 3, title: '提示' }, function(boxIndex) {
                axios.post(yadjet.rbac.urls.permissions.delete.replace('_name', name))
                    .then(function(response) {
                        vm.permissions.splice(index, 1);
                        for (var i in vm.pendingPermissions) {
                            if (vm.pendingPermissions[i].name == name) {
                                vm.pendingPermissions[i].active = true;
                                break;
                            }
                        }
                    })
                    .catch(function(error) {
                    });

                layer.close(boxIndex);
            });
        },
        // 关闭弹窗
        closeWindow: function() {
            vm.window.rolePermissions = false;
            vm.activeObject.userId = 0;
            vm.activeObject.role = undefined;
        }
    },
    computed: {
        // 当前用户的角色
        userRoles: function() {
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
        rolePermissions: function() {
            var permissions = [],
                permission,
                keyword = this.role.keyword;
            for (var i in this.permissions.raw) {
                permission = clone(this.permissions.raw[i]);
                permission.active = false;
                if (keyword && permission.name.indexOf(keyword) === -1) {
                    continue;
                }
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

$(function() {
    $('.rbac-tabs-common li a').on('click', function() {
        var $t = $(this);
        $t.parent().addClass('active').siblings().removeClass('active');
        $('#rbac-app .panel').hide();
        $('#rbac-app #' + $t.attr('data-toggle')).show();
        vm.closeWindow();

        return false;
    });

    // 角色提交表单
    $('#rbac-submit-role').on('click', function() {
        $.ajax({
            type: 'POST',
            url: yadjet.rbac.urls.roles.save,
            data: $('#rbac-role-form form').serialize(),
            returnType: 'json',
            success: function(response) {
                if (response.success) {
                    // vm.roles[response.data.name] = response.data;
                    if (response.data.insert) {
                        vm.roles.push(response.data.role);
                    } else {
                        for (var key in vm.roles) {
                            if (vm.roles[key].name == response.data.role.name) {
                                vm.roles.splice(key, 1, response.data.role);
                                break;
                            }
                        }
                    }
                } else {
                    layer.alert(response.error.message);
                }
            }, error: function(XMLHttpRequest, textStatus, errorThrown) {
                layer.alert('ERROR ' + XMLHttpRequest.status + ' 错误信息： ' + XMLHttpRequest.responseText);
            }
        });

        return false;
    });

    $('#rbac-submit-permission').on('click', function() {
        $.ajax({
            type: 'POST',
            url: yadjet.rbac.urls.permissions.create,
            data: $('#rbac-permission-form form').serialize(),
            returnType: 'json',
            success: function(response) {
                if (response.success) {
                    vm.permissions.push(response.data);
                } else {
                    layer.alert(response.error.message);
                }
            }, error: function(XMLHttpRequest, textStatus, errorThrown) {
                layer.alert('ERROR ' + XMLHttpRequest.status + ' 错误信息： ' + XMLHttpRequest.responseText);
            }
        });

        return false;
    });
});