const Url = require('../../../utils/Url.js'),
    Identity = require('../../../utils/Identity.js');

Page({
    data: {
        form: {}
    },
    onLoad: function(options) {
        if (Identity.isGuest()) {
            Identity.toLoginPage();
        } else {
            wx.showLoading({ title: '数据加载中...' });
            wx.request({
                url: Url.toRoute('/account/view'),
                success: res => {
                    const resp = res.data;
                    if (resp.success) {
                        this.setData({
                            form: {
                                username: resp.data.username,
                                nickname: resp.data.nickname,
                                real_name: resp.data.real_name,
                                mobile_phone: resp.data.mobile_phone,
                            },
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
                    wx.hideLoading({});
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
    },
    // 修改个人资料
    updateHandle: function(e) {
        let isValid = true,
            message = '';
        const form = this.data.form;
        if (!form.nickname) {
            isValid = false;
            message = '请输入昵称';

        }
        if (isValid && !form.real_name) {
            isValid = false;
            message = '请输入姓名';
        }
        if (isValid && !form.mobile_phone) {
            isValid = false;
            message = '请输入手机号码';
        }
        if (!isValid) {
            wx.showToast({
                title: message,
                icon: "none",
            });
        } else {
            wx.showLoading({
                title: '处理中...'
            });
            delete form['username'];
            wx.request({
                url: Url.toRoute('/account/update'),
                data: form,
                method: "PUT",
                success: res => {
                    const resp = res.data;
                    if (resp.success) {
                        let identity = wx.getStorageSync('identity');
                        if (identity) {
                            identity = { ...identity, ...form };
                            wx.setStorageSync('identity', identity);
                        }
                        wx.showToast({
                            title: '修改成功',
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
                    wx.hideLoading({});
                }
            });
        }
    }
});
