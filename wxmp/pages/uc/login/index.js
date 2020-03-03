const Url = require('../../../utils/Url.js'),
    GlobalData = require('../../../utils/GlobalData.js'),
    Identity = require('../../../utils/Identity.js');

const LOGIN_TYPE_ACCOUNT = 'account',
    LOGIN_TYPE_MOBILE_PHONE = 'mobile_phone',
    LOGIN_TYPE_MOBILE_CAPTCHA = 'captcha',
    LOGIN_TYPE_MOBILE_ACCESS_TOKEN = 'access_token';

Page({
    data: {
        form: {
            type: LOGIN_TYPE_ACCOUNT,
            username: null,
            password: null,
        }
    },
    onLoad: function() {
        if (!Identity.isGuest()) {
            wx.redirectTo({
                url: '/pages/uc/index/index',
            });
        }
    },
    // 登录
    loginHandle: function() {
        console.info('loginHandle');
        let isValid = true,
            message = '';
        const form = this.data.form;
        if (!form.username) {
            isValid = false;
            message = '请输入登录帐号';
        }

        if (isValid && !form.password) {
            isValid = false;
            message = '请输入登录密码';
        }
        if (!isValid) {
            wx.showToast({
                icon: "none",
                title: message,
            });
        } else {
            wx.showLoading();
            wx.request({
                url: Url.toRoute('/passport/login'),
                data: form,
                method: 'POST',
                success: res => {
                    const resp = res.data;
                    if (resp.success) {
                        wx.setStorageSync('identity', {
                            id: resp.data.id,
                            username: resp.data.username,
                            nickname: resp.data.nickname,
                            real_name: resp.data.real_name,
                            avatar: resp.data.avatar ? resp.data.avatar : GlobalData.get('config.asset.defaultAvatar'),
                            access_token: resp.data.access_token,
                        });
                        wx.showToast({
                            title: '登录成功',
                        });
                        wx.redirectTo({
                            url: '/pages/uc/index/index',
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
    // 微信登录
    wxLoginHandle: function() {
        wx.login({
            success(res) {
                if (res.code) {
                    console.info("Code: " + res.code);
                    wx.request({
                        url: Url.toRoute('/wx-app/login'),
                        data: {
                            code: res.code
                        },
                        success: function(response) {
                            const resp = response.data;
                            if (resp.success) {
                                wx.setStorageSync('identity', {
                                    id: resp.data.id,
                                    username: resp.data.username,
                                    nickname: resp.data.nickname,
                                    real_name: resp.data.real_name,
                                    avatar: resp.data.avatar ? resp.data.avatar : GlobalData.get('config.asset.defaultAvatar'),
                                    access_token: resp.data.access_token,
                                });
                                wx.showToast({
                                    title: '登录成功',
                                });
                                wx.redirectTo({
                                    url: '/pages/uc/index/index',
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
                        }
                    });
                } else {
                    console.log('登录失败！' + res.errMsg);
                    wx.showModal({
                        title: '错误提示',
                        content: res.errMsg,
                        showCancel: false,
                    });
                }
            }
        })
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
