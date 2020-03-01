const util = require('../../utils/util.js')

Component({
    properties: {
        items: {
            type: Array,
            value: [],
        }
    },
    data: {
        pages: [
            '/pages/index/index',
            '/pages/news/index',
        ],
        defaultItems: [
            {
                "text": "首页",
                "iconPath": "/static/images/icon-tabbar.png",
                "selectedIconPath": "/static/images/icon-tabbar-active.png",
                dot: true
            },
            {
                "text": "资讯",
                "iconPath": "/static/images/icon-tabbar.png",
                "selectedIconPath": "/static/images/icon-tabbar-active.png",
                badge: '99+'
            }
        ],
        activeIndex: -1,
    },
    lifetimes: {
        attached: function() {
            let page = util.currentPage();
            if (page) {
                page = `/${page}`;
            }
            if (page) {
                for (let i in this.data.pages) {
                    if (this.data.pages[i] === page) {
                        this.setData({ activeIndex: i });
                        break;
                    }
                }
            }

        },
        detached: function() {
            // 在组件实例被从页面节点树移除时执行
        },
    },
    methods: {
        tabChange: function(e) {
            console.info(e);
            const detail = e.detail;
            console.info("Tab " + detail.index + " change");
            console.info("Tab item", detail.item);
            wx.navigateTo({
                url: this.data.pages[detail.index]
            });
        }
    }
});