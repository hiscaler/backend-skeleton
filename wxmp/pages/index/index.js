const app = getApp();
const Url = require('../../utils/Url.js');

Page({
    data: {
        motto: 'Hello World',
        userInfo: {},
        hasUserInfo: false,
        canIUse: wx.canIUse('button.open-type.getUserInfo'),
        latestNewsItems: [],
    },
    //事件处理函数
    bindViewTap: function() {
        wx.navigateTo({
            url: '../logs/logs'
        })
    },
    onLoad: function() {
        // Latest news itmes
        wx.request({
            url: Url.toRoute('/news/default', {
                fields: "id,title,description,is_picture_news,picture_path,published_at",
                limit: 5,
            }),
            success: res => {
                const resp = res.data;
                if (resp.success) {
                    this.setData({
                        latestNewsItems: resp.data.items,
                    });
                } else {
                    wx.showModal({
                        title: '提示',
                        content: resp.error.message,
                        showCancel: false,
                    });
                }
            }
        });

        if (app.globalData.userInfo) {
            this.setData({
                userInfo: app.globalData.userInfo,
                hasUserInfo: true
            });
        } else if (this.data.canIUse) {
            // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
            // 所以此处加入 callback 以防止这种情况
            app.userInfoReadyCallback = res => {
                this.setData({
                    userInfo: res.userInfo,
                    hasUserInfo: true
                });
            }
        } else {
            // 在没有 open-type=getUserInfo 版本的兼容处理
            wx.getUserInfo({
                success: res => {
                    app.globalData.userInfo = res.userInfo;
                    this.setData({
                        userInfo: res.userInfo,
                        hasUserInfo: true
                    });
                }
            })
        }
    },
    getUserInfo: function(e) {
        console.log(e);
        app.globalData.userInfo = e.detail.userInfo;
        this.setData({
            userInfo: e.detail.userInfo,
            hasUserInfo: true
        });
    }
});
