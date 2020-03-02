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
    // 注销
    logoutHandle: function() {
        wx.showActionSheet({
            itemList: ['确定'],
            success(res) {
                wx.setStorageSync('identity', null);
                wx.redirectTo({
                    url: GlobalData.get('config.homeUrl', '/pages/index/index')
                });
            },
            fail(res) {
                console.log(res.errMsg)
            }
        });

    }
});
