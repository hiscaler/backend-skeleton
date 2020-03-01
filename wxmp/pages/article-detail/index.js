const Url = require('../../utils/Url.js');

Page({
    data: {
        item: {},
    },
    onLoad: function(options) {
        wx.showLoading();
        let id = null;
        if (options.hasOwnProperty('id')) {
            id = options.id;
        }
        if (id) {
            wx.request({
                url: Url.toRoute('/article/default/view', { id }),
                success: res => {
                    const resp = res.data;
                    if (resp.success) {
                        this.setData({
                            item: resp.data,
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
        } else {
            wx.hideLoading();
            wx.showModal({
                title: '提示',
                content: "缺少 id 参数值。",
                showCancel: false,
            });
        }
    }
});