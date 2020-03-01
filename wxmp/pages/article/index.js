const Url = require('../../utils/Url.js');

Page({
    data: {
        items: [],
    },
    onShow: function() {
        wx.request({
            url: Url.toRoute('/article'),
            success: res => {
                const resp = res.data;
                if (resp.success) {
                    this.setData({
                        items: resp.data.items,
                    });
                    console.info(resp.data.items);
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