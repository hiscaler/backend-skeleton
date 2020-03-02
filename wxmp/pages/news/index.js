const Url = require('../../utils/Url.js');

Page({
    data: {
        items: [],
        pagination: {},
    },
    onLoad: function(options) {
        let params = {};
        if (options && options.hasOwnProperty("categoryId")) {
            params['categoryId'] = options.categoryId;
        }
        wx.request({
            url: Url.toRoute('/news/default', params),
            success: res => {
                const resp = res.data;
                if (resp.success) {
                    this.setData({
                        items: resp.data.items,
                        pagination: resp.data._meta,
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
    }
});