const formatTime = date => {
    const year = date.getFullYear();
    const month = date.getMonth() + 1;
    const day = date.getDate();
    const hour = date.getHours();
    const minute = date.getMinutes();
    const second = date.getSeconds();

    return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':');
};

const formatNumber = n => {
    n = n.toString();
    return n[1] ? n : '0' + n;
};

/**
 * 获取当前页面
 * @returns {null|string}
 */
const currentPage = () => {
    let page = null;
    const pages = getCurrentPages(),
        n = pages.length;
    if (n) {
        page = pages[n - 1].route;
    }
    console.info(`Current page is ${page}`);

    return page;
};

module.exports = {
    formatTime: formatTime,
    currentPage: currentPage,
};
