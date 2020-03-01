const Url = require('../../../utils/Url.js');

Page({
    data: {
        form: {
            old_password: null,
            new_password: null,
            confirm_password: null,
        }
    },
    changePasswordHandle: function() {
        let isValid = true,
            message = '';
        const form = this.data.form;
        if (form.old_password) {
            isValid = false;
            message = '请输入旧密码';

        }
        if (isValid && (!form.new_password || !form.confirm_password)) {
            isValid = false;
            message = '请输入新密码和确认密码';
        }
        if (isValid && form.new_password != form.confirm_password) {
            isValid = false;
            message = '新密码和确认密码不一致';
        }
        if (!isValid) {
            wx.showToast({
                title: '错误提示',
                content: message,
            });
        } else {
            wx.showLoading();
            wx.request({
                url: Url.toRoute('/passport/change-password'),
                data: form,
                success: res => {
                    const resp = res.data;
                    if (resp.success) {
                        wx.showModal({
                            title: '提示',
                            content: '密码修改成功，下次登录请使用新密码。',
                            showCancel: false,
                        });
                    } else {
                        wx.showModal({
                            title: '提示',
                            content: resp.error.message,
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
