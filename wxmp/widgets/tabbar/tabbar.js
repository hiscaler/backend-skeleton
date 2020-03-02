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
            '/pages/article/index',
            [
                '/pages/news/index',
                '/pages/news-detail/index',
            ],
            [
                '/pages/uc/index/index',
                '/pages/uc/account/index',
                '/pages/uc/account/index',
                '/pages/uc/login/index',
            ],
        ],
        defaultItems: [
            {
                "text": "首页",
                "iconPath": "/static/images/icon-tabbar.png",
                "selectedIconPath": "/static/images/icon-tabbar-active.png",
                dot: true
            },
            {
                "text": "文章",
                "iconPath": "/static/images/icon-tabbar.png",
                "selectedIconPath": "/static/images/icon-tabbar-active.png",
            },
            {
                "text": "资讯",
                "iconPath": "/static/images/icon-tabbar.png",
                "selectedIconPath": "/static/images/icon-tabbar-active.png",
                badge: '99+'
            },
            {
                "text": "我的",
                "iconPath": "/static/images/icon-tabbar.png",
                "selectedIconPath": "/static/images/icon-tabbar-active.png",
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
                let activeIndex = null;
                for (let i in this.data.pages) {
                    let children = this.data.pages[i];
                    if (!Array.isArray(children)) {
                        children = [children];
                    }
                    for (let j in children) {
                        if (children[j] === page) {
                            activeIndex = i;
                            break;
                        }
                    }
                    if (activeIndex !== null) {
                        break;
                    }

                }
                if (activeIndex !== null) {
                    this.setData({ activeIndex });
                }
            }
        },
    },
    methods: {
        tabChange: function(e) {
            console.info(e);
            const detail = e.detail;
            console.info("Tab " + detail.index + " change");
            console.info("Tab item", detail.item);
            let url = this.data.pages[detail.index];
            if (Array.isArray(url)) {
                url = url[0];
            }
            wx.redirectTo({
                url
            });
        }
    }
});