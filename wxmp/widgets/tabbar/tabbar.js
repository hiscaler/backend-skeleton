Component({
    properties: {
        items: {
            type: Array,
            value: [],
        }
    },
    data: {
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
    },
    methods: {
        tabChange: function(e) {
            console.info(e);
            const detail = e.detail;
            console.info("Tab " + detail.index + " change");
            console.info("Tab item", detail.item);
            const pages = [
                '/pages/index/index',
                '/pages/news/index',
            ];
            wx.redirectTo({
                url: pages[detail.index]
            });
        }
    }
});