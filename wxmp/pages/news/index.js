const Url = require('../../utils/Url.js');

Page({
    data: {
        items: [
            {
                id: 1,
                title: "第 1 条新闻",
                description: "第 1 条新闻描叙",
            },
            {
                id: 2,
                title: "第 2 条新闻",
                description: "第 2 条新闻描叙",
            }
        ],
        pagination: {},
    },
    onLoad: function(options) {
        let params = {};
        if (options && options.hasOwnProperty("categoryId")) {
            params['categoryId'] = options.categoryId;
        }
        wx.request({
            url: Url.toRoute('/news/index', params),
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
        })
    }
});