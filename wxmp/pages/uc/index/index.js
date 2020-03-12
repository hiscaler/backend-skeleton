const Url = require('../../../utils/Url.js'),
    Identity = require('../../../utils/Identity.js'),
    GlobalData = require('../../../utils/GlobalData.js');

Page({
    data: {
        identity: {}
    },
    onLoad: function(options) {
        if (Identity.isGuest()) {
            Identity.toLoginPage();
        } else {
            this.setData({
                identity: wx.getStorageSync('identity'),
            });
        }
    },
    onShow: function() {
        if (Identity.isGuest()) {
            Identity.toLoginPage();
        }
    },
    // 注销
    logoutHandle: function() {
        wx.showModal({
            title: '提示',
            content: '您是否确定退出？',
            success: function(sm) {
                if (sm.confirm) {
                    wx.removeStorage({
                        key: 'identity',
                        success(res) {
                            wx.redirectTo({
                                url: GlobalData.get('config.homeUrl', '/pages/index/index')
                            });
                        }
                    });
                } else if (sm.cancel) {

                }
            }
        });
    }
});
