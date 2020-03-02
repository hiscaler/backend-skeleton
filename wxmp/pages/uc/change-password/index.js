const Url = require('../../../utils/Url.js');

Page({
    data: {
        form: {
            old_password: null,
            password: null,
            confirm_password: null,
        }
    },
    // 修改密码
    changePasswordHandle: function() {
        let isValid = true,
            message = '';
        const form = this.data.form;
        if (!form.old_password) {
            isValid = false;
            message = '请输入旧密码';

        }
        if (isValid && (!form.password || !form.confirm_password)) {
            isValid = false;
            message = '请输入新密码和确认密码';
        }
        if (isValid && form.password != form.confirm_password) {
            isValid = false;
            message = '新密码和确认密码不一致';
        }
        if (isValid && form.old_password === form.password) {
            isValid = false;
            message = '旧密码和新密码一致，无需更换';
        }
        if (!isValid) {
            wx.showToast({
                title: message,
                icon: "none",
            });
        } else {
            wx.showLoading();
            wx.request({
                url: Url.toRoute('/passport/change-password'),
                data: form,
                method: "POST",
                success: res => {
                    const resp = res.data;
                    if (resp.success) {
                        wx.showModal({
                            title: '提示',
                            content: '密码修改成功，下次登录请使用新密码。',
                            showCancel: false,
                        });
                    } else {
                        let message = '';
                        const error = resp.error;
                        if (Array.isArray(error)) {
                            for (let i in error) {
                                message = error[i].message;
                                break;
                            }
                        } else {
                            message = error.message;
                        }
                        wx.showModal({
                            title: '提示',
                            content: message,
                            showCancel: false,
                        });
                    }
                },
                complete: function(res) {
                    wx.hideLoading();
                }
            });
        }
    },
    // 数据双向绑定
    bindField: function(e) {
        let dataset = e.currentTarget.dataset,
            name = dataset.name,
            value = e.detail.value,
            form = this.data.form;
        form[name] = value;
        this.setData({
            form,
        });
    }
});
