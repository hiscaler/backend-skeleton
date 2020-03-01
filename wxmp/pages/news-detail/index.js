const app = getApp()
const items = [
    {
        id: 1,
        title: "第 1 条新闻",
        description: "第 1 条新闻描叙",
        content: "第 1 条新闻正文内容",
    },
    {
        id: 2,
        title: "第 2 条新闻",
        description: "第 2 条新闻描叙",
        content: "第 2 条新闻正文内容",
    }
];

Page({
    data: {
        item: items[0],
    },

    onShow: function(options) {
        console.info(options);
    }
});
