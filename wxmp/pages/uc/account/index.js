const Url = require('../../../utils/Url.js');

Page({
    data: {
        identity: {},
        form: {}
    },
    onLoad: function(options) {
        wx.showLoading();
        wx.request({
            url: Url.toRoute('/account/default'),
            success: res => {
                const resp = res.data;
                if (resp.success) {
                    this.setData({
                        identity: resp.data,
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
