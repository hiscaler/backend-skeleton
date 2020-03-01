const Url = require('../../utils/Url.js');

Page({
    data: {
        item: {},
    },
    onLoad: function(options) {
        wx.request({
            url: Url.toRoute('/article/default/view', { id: options.id }),
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
                    });
                }
            }
        });
    }
});